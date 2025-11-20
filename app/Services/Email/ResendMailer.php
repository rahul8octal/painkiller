<?php 

class ResendMailer {
    public function send(string $to, string $subject, string $html) {
        Http::withToken(env('RESEND_API_KEY'))
            ->post('https://api.resend.com/emails', [
                'from' => 'Painkiller <noreply@yourdomain.com>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $html,
            ])->throw();
    }
}
