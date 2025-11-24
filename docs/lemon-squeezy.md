# Lemon Squeezy Payments & Subscriptions (Step‑by‑Step)

The [lmsqueezy/laravel](https://github.com/lmsqueezy/laravel) package wraps the [Lemon Squeezy](https://www.lemonsqueezy.com/) REST API so you can sell one‑off products and recurring subscriptions directly from this application. Follow the checklist below to go from a plain Laravel install to a working billing pipeline that also handles webhooks securely.

---

## 1. Create the Lemon Squeezy side

1. Create a Lemon Squeezy account and store.  
2. Inside the Lemon Squeezy dashboard create:
   - **Products/Variants** for every plan or one‑off payment you need. Each variant exposes an `variant_id` that you will pass from Laravel.
   - **Payment links** (optional) if you prefer redirecting to the hosted checkout instead of creating sessions with the API.
3. Generate the following credentials from *Settings → API* and *Settings → Webhooks*:
   - `LEMON_SQUEEZY_API_KEY` – personal access token with “Read & Write” scope.
   - `LEMON_SQUEEZY_STORE_ID` – numeric ID for the store that owns your products.
   - `LEMON_SQUEEZY_SIGNING_SECRET` – secret that Lemon Squeezy uses to sign webhook payloads.
4. In the Lemon Squeezy dashboard add a webhook endpoint (e.g. `https://your-domain.com/webhooks/lemon-squeezy`) and subscribe to the events you care about (see §7).

---

## 2. Install the official Laravel package

```bash
# from the project root
composer require lmsqueezy/laravel

# publish configuration + database tables
php artisan vendor:publish --tag=lemon-squeezy-config
php artisan vendor:publish --tag=lemon-squeezy-migrations
php artisan migrate
```

The package is auto‑discovered, so you do **not** need to register the service provider manually.

---

## 3. Configure environment & config

Add the following variables to `.env` (or the respective secrets manager):

```dotenv
LEMON_SQUEEZY_API_KEY=sk_live_***
LEMON_SQUEEZY_STORE_ID=12345
LEMON_SQUEEZY_SIGNING_SECRET=whsec_***
LEMON_SQUEEZY_DEFAULT_SUCCESS_URL=${APP_URL}/billing/success
LEMON_SQUEEZY_DEFAULT_CANCEL_URL=${APP_URL}/billing/cancel
```

The published `config/lemon-squeezy.php` file reads these values. Update it if you need per‑environment store IDs or different webhook URL segments.

Run `php artisan config:clear` after editing environment variables so Laravel picks up the changes.

---

## 4. Make your billable model ready

1. Open `app/Models/User.php` (or whatever model owns a subscription) and add the Billable trait:

    ```php
    use LemonSqueezy\Laravel\Concerns\Billable;

    class User extends Authenticatable
    {
        use Billable;
    }
    ```

2. Re-run the migrations if you published them later. The package ships tables for:
   - `lemon_squeezy_customers`
   - `lemon_squeezy_subscriptions`
   - `lemon_squeezy_subscription_items`

Those tables keep Lemon Squeezy IDs in sync with your local models.

---

## 5. Creating checkout sessions (one‑time purchases)

```php
use LemonSqueezy\Laravel\Facades\LemonSqueezy;

class CheckoutController
{
    public function __invoke(Request $request)
    {
        $checkout = LemonSqueezy::checkout()->create([
            'store_id' => config('lemon-squeezy.store'),
            'variant_id' => $request->integer('variant_id'),
            'checkout_data' => [
                'email' => $request->user()->email,
                'name' => $request->user()->name,
            ],
            'checkout_options' => [
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing.cancel'),
            ],
            'custom' => [
                'user_id' => $request->user()->getKey(),
            ],
        ]);

        return redirect($checkout['data']['attributes']['url']);
    }
}
```

The `custom` payload is echoed back by Lemon Squeezy and is useful for correlating payments in webhook handlers. You can also skip the API entirely and redirect users to the hosted payment link defined in the dashboard—just store the link in config or the DB.

---

## 6. Starting & managing subscriptions

```php
public function subscribe(Request $request, int $variantId)
{
    $user = $request->user();

    if (! $user->lemonSqueezyCustomerId()) {
        $user->createAsLemonSqueezyCustomer([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    $subscription = $user->newSubscription('default', $variantId)
        ->allowMultipleSubscriptions()
        ->trialDays(14)
        ->create([
            'success_url' => route('billing.success'),
            'cancel_url' => route('billing.cancel'),
        ]);

    return redirect($subscription->checkoutUrl());
}
```

Key helpers shipped with the package:

- `createAsLemonSqueezyCustomer(array $attributes)` – syncs the local user with a Lemon Squeezy customer ID.
- `newSubscription($name, $variantId)` – prepares a subscription builder.
- `Subscription` model methods like `cancel()`, `resume()`, `swap($newVariantId)`, `onGracePeriod()` mirror Cashier’s API for a smoother developer experience.

Store the `variant_id` for each plan in config (e.g. `config/billing.php`) so you do not hardcode IDs inside controllers.

---

## 7. Webhook endpoint

1. Add a signed route that points to the package’s webhook controller (or your own):

    ```php
    use LemonSqueezy\Laravel\Http\Controllers\WebhookController;

    Route::post('/webhooks/lemon-squeezy', WebhookController::class)
        ->name('lemon-squeezy.webhook');
    ```

    > If you need custom logic, publish the controller with `php artisan vendor:publish --tag=lemon-squeezy-webhook-controller` or copy the source and extend it.

2. Lemon Squeezy includes the following headers; the package validates them automatically:
   - `X-Lemon-Signature`
   - `X-Lemon-Timestamp`

3. Listen for the events relevant to your workflow. Common ones:
   - `order_created`, `order_refunded`
   - `subscription_created`, `subscription_cancelled`, `subscription_paused`, `subscription_expired`
   - `subscription_payment_success`, `subscription_payment_failed`

4. Handle the events by listening to Laravel events dispatched by the package (`LemonSqueezy\Laravel\Events\*`). Example:

    ```php
    Event::listen(SubscriptionCreated::class, function ($event) {
        $event->subscription->syncCurrentPeriod();
        // enable features / send welcome email
    });
    ```

5. Point the webhook you configured in the Lemon Squeezy dashboard to `/webhooks/lemon-squeezy`. Use `php artisan route:list | grep lemon` to confirm the route exists in production.

---

## 8. Local testing tips

- Use `sail share`, `ngrok`, or `valet share` to expose the Laravel site while developing webhooks.
- Set `LEMON_SQUEEZY_API_KEY` and other secrets in `.env.testing` if you run feature tests against the sandbox API.
- Feature-test subscription flows by faking webhooks:

    ```php
    LemonSqueezy::fake();
    LemonSqueezy::assertCheckoutCreated();
    ```

- Log raw webhook payloads in a temporary table (or `storage/logs/laravel.log`) when first integrating, then remove once stable.

---

## 9. Deployment checklist

- [ ] Secrets exist in your production environment.
- [ ] `php artisan migrate --force` ran after deploying to create the billing tables.
- [ ] Webhook endpoint is reachable from the public internet (double-check CSRF exemptions if you run `VerifyCsrfToken` middleware).
- [ ] Supervisors/queues are configured if you queue webhook jobs.
- [ ] Billing routes are protected by auth middleware to avoid exposing subscription management to anonymous users.

With these steps completed you have recurring payments, one‑off payments, and secure webhook processing powered by Lemon Squeezy. Refer to the upstream repository for the full surface area of the API plus advanced flows such as usage‑based billing or customer portals.
