<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'api' => [
            'default' => '60,1', // 60 requests per minute
            'auth' => '5,1', // 5 login attempts per minute
            'checkout' => '10,1', // 10 checkout requests per minute
            'search' => '30,1', // 30 search requests per minute
        ],
        
        'web' => [
            'default' => '120,1', // 120 requests per minute
            'forms' => '10,1', // 10 form submissions per minute
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | IP Throttling
    |--------------------------------------------------------------------------
    */
    'ip_throttling' => [
        'enabled' => env('IP_THROTTLING_ENABLED', true),
        'blacklist' => explode(',', env('IP_BLACKLIST', '')),
        'whitelist' => explode(',', env('IP_WHITELIST', '127.0.0.1,::1')),
        'max_attempts' => env('IP_MAX_ATTEMPTS', 100),
        'decay_minutes' => env('IP_DECAY_MINUTES', 5),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | GDPR Compliance
    |--------------------------------------------------------------------------
    */
    'gdpr' => [
        'data_retention' => [
            'orders' => 7, // years (financial/legal requirement)
            'user_data' => 2, // years after last activity
            'logs' => 90, // days
            'sessions' => 30, // days
            'exports' => 7, // days
        ],
        
        'anonymization' => [
            'enabled' => true,
            'auto_after_days' => 730, // 2 years of inactivity
        ],
        
        'export' => [
            'enabled' => true,
            'max_size_mb' => 100,
            'formats' => ['json', 'csv'],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'hsts_enabled' => env('HSTS_ENABLED', true),
        'hsts_max_age' => 31536000, // 1 year
        'csp_enabled' => env('CSP_ENABLED', true),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Encryption & Secrets
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'algorithm' => 'AES-256-CBC',
        'sensitive_fields' => [
            'users' => ['password', 'remember_token'],
            'sellers' => ['bank_account', 'tax_id'],
            'orders' => ['payment_details'],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | WAF Recommendations
    |--------------------------------------------------------------------------
    */
    'waf' => [
        'recommended_rules' => [
            'SQL Injection Protection',
            'XSS Protection',
            'CSRF Protection',
            'File Upload Validation',
            'Rate Limiting',
            'Geo-blocking (if required)',
            'Bot Protection',
        ],
        
        'providers' => [
            'cloudflare' => 'https://www.cloudflare.com/waf/',
            'aws_waf' => 'https://aws.amazon.com/waf/',
            'akamai' => 'https://www.akamai.com/products/kona-site-defender',
        ],
    ],
];
