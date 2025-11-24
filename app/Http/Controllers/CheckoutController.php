<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LemonSqueezy\Laravel\LemonSqueezy;

class CheckoutController extends Controller
{
    /**
     * Create a new checkout session.
     */
    public function create(Request $request, string $variantId)
    {
        $user = $request->user();

        // Ensure customer exists
        if (! $user->customer) {
            $user->createCustomer([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        return $user->checkout($variantId)
            ->withCustomData(['user_id' => $user->id])
            ->redirectTo(config('app.frontend_url') . '/pricing');
    }

    /**
     * Get available products.
     */
    public function products()
    {
        try {
            $products = LemonSqueezy::api('GET', 'products', [
                'include' => 'variants',
            ])->json();

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products'], 500);
        }
    }
}
