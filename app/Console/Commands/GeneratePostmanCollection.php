<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePostmanCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-postman-collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Postman collection for the API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $collection = [
            'info' => [
                'name' => 'E-commerce API',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                'description' => 'API for e-commerce platform'
            ],
            'item' => [
                [
                    'name' => 'Authentication',
                    'item' => [
                        [
                            'name' => 'Register',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'name' => 'John Doe',
                                        'email' => 'john@example.com',
                                        'password' => 'password123',
                                        'password_confirmation' => 'password123',
                                        'phone' => '+1234567890'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/register',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'register']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Login',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'email' => 'john@example.com',
                                        'password' => 'password123'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/login',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'login']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Logout',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ],
                                    [
                                        'key' => 'Authorization',
                                        'value' => 'Bearer {{auth_token}}'
                                    ]
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/logout',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'logout']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Phone Request OTP',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'phone' => '+1234567890'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/phone/request-otp',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'phone', 'request-otp']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Phone Verify OTP',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'phone' => '+1234567890',
                                        'otp' => '123456',
                                        'name' => 'John Doe'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/phone/verify-otp',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'phone', 'verify-otp']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Social Login',
                            'request' => [
                                'method' => 'POST',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'provider' => 'google',
                                        'provider_id' => '123456789',
                                        'name' => 'John Doe',
                                        'email' => 'john@example.com',
                                        'avatar' => 'https://example.com/avatar.jpg'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/auth/social/login',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'auth', 'social', 'login']
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'User',
                    'item' => [
                        [
                            'name' => 'Get Current User',
                            'request' => [
                                'method' => 'GET',
                                'header' => [
                                    [
                                        'key' => 'Authorization',
                                        'value' => 'Bearer {{auth_token}}'
                                    ]
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/me',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'me']
                                ]
                            ]
                        ],
                        [
                            'name' => 'Update Current User',
                            'request' => [
                                'method' => 'PUT',
                                'header' => [
                                    [
                                        'key' => 'Content-Type',
                                        'value' => 'application/json'
                                    ],
                                    [
                                        'key' => 'Authorization',
                                        'value' => 'Bearer {{auth_token}}'
                                    ]
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => json_encode([
                                        'name' => 'John Updated',
                                        'email' => 'john.updated@example.com',
                                        'phone' => '+1234567890'
                                    ], JSON_PRETTY_PRINT)
                                ],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/me',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'me']
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Categories',
                    'item' => [
                        [
                            'name' => 'Get All Categories',
                            'request' => [
                                'method' => 'GET',
                                'header' => [],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/categories',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'categories']
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Products',
                    'item' => [
                        [
                            'name' => 'Get All Products',
                            'request' => [
                                'method' => 'GET',
                                'header' => [],
                                'url' => [
                                    'raw' => '{{base_url}}/api/v1/products',
                                    'host' => ['{{base_url}}'],
                                    'path' => ['api', 'v1', 'products']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => 'http://localhost:8000',
                    'type' => 'string'
                ],
                [
                    'key' => 'auth_token',
                    'value' => '',
                    'type' => 'string'
                ]
            ]
        ];

        $json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $filePath = storage_path('api-docs/ecommerce-api-postman-collection.json');
        
        file_put_contents($filePath, $json);
        
        $this->info("Postman collection generated successfully at: {$filePath}");
    }
}