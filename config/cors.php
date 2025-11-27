<?php
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // **Must be your React dev URL**, not '*'
    'allowed_origins' => ['http://localhost:5173'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => ['*'],
    // Allow cookies (important!)
    'supports_credentials' => true,

];
