<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
     /*
    |--------------------------------------------------------------------------
    | 🧠 OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Used for all AI processing – normalization, scoring, enrichment, and
    | idea generation. Supports GPT-4 or GPT-5 models.
    |
    */

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-5'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],


    /*
    |--------------------------------------------------------------------------
    | 📈 Enrichment / Signals Configuration
    |--------------------------------------------------------------------------
    |
    | Used by SignalEnrichmentService to gather external metrics
    | like Google Trends, GitHub issues, and Reddit mentions.
    |
    */

    'signals' => [
        'google_trends_key' => env('GOOGLE_TRENDS_API_KEY', null),
        'serpapi_key' => env('SERPAPI_KEY', null),
        'github_token' => env('GITHUB_TOKEN', null),
        'default_limit' => env('SIGNALS_LIMIT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | ✉️ Notification Services (Resend or Loops)
    |--------------------------------------------------------------------------
    |
    | Handles sending alerts and weekly idea digests.
    |
    */

    'resend' => [
        'key' => env('RESEND_API_KEY'),
        'from' => env('RESEND_FROM', 'Painkiller <noreply@painkiller.ai>'),
        'admin_recipients' => explode(',', env('ALERT_EMAILS', 'admin@painkiller.ai')),
    ],

    'loops' => [
        'api_key' => env('LOOPS_API_KEY'),
        'newsletter_id' => env('LOOPS_NEWSLETTER_ID'),
        'idea_of_week_segment' => env('LOOPS_SEGMENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ⚙️ Painkiller Project Settings
    |--------------------------------------------------------------------------
    |
    | Centralized tuning for your pipeline — thresholds, scheduling, etc.
    |
    */

    'painkiller' => [
        'auto_approve_threshold' => env('AUTO_APPROVE_SCORE', 8),
        'fetch_schedule' => env('FETCH_CRON', '0 6 * * *'),
        'process_schedule' => env('PROCESS_CRON', '30 6 * * *'),
        'notify_schedule' => env('NOTIFY_CRON', '0 9 * * 5'),
        'max_daily_fetch' => env('FETCH_LIMIT', 100),
        'process_batch' => env('PROCESS_BATCH', 25),
        'enable_debug_logs' => env('PAINKILLER_DEBUG', false),
    ],

];
