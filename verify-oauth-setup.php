#!/usr/bin/env php
<?php

/**
 * Google OAuth Setup Verification Script
 * Run this script to verify your Google OAuth setup is correct
 * 
 * Usage: php verify-oauth-setup.php
 */

echo "\n=== Google OAuth Setup Verification ===\n\n";

// Check .env file
echo "1. Checking .env configuration...\n";
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "   ❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);
$requiredVars = [
    'GOOGLE_CLIENT_ID',
    'GOOGLE_CLIENT_SECRET',
    'FRONTEND_URL',
    'APP_URL'
];

$missing = [];
foreach ($requiredVars as $var) {
    if (strpos($envContent, $var) === false) {
        $missing[] = $var;
    }
}

if (!empty($missing)) {
    echo "   ❌ Missing environment variables: " . implode(', ', $missing) . "\n";
    exit(1);
}
echo "   ✅ All required environment variables found\n\n";

// Check database connection
echo "2. Checking database connection...\n";
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check users table structure
echo "3. Checking users table structure...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM users");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['google_id', 'avatar', 'password'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (!empty($missingColumns)) {
        echo "   ❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
        echo "   Run: php artisan migrate\n";
        exit(1);
    }
    
    // Check avatar column type
    $avatarColumn = collect($columns)->firstWhere('Field', 'avatar');
    if ($avatarColumn && strpos(strtolower($avatarColumn->Type), 'text') === false) {
        echo "   ⚠️  Warning: avatar column should be TEXT type, currently: " . $avatarColumn->Type . "\n";
        echo "   Run: php artisan migrate to fix this\n";
    }
    
    echo "   ✅ Users table structure is correct\n\n";
} catch (Exception $e) {
    echo "   ❌ Error checking table structure: " . $e->getMessage() . "\n";
    exit(1);
}

// Check routes
echo "4. Checking OAuth routes...\n";
$routes = Route::getRoutes();
$oauthRoutes = ['auth/google', 'auth/google/callback', 'auth/logout'];
$foundRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (in_array($uri, $oauthRoutes)) {
        $foundRoutes[] = $uri;
    }
}

if (count($foundRoutes) !== count($oauthRoutes)) {
    echo "   ❌ Missing OAuth routes\n";
    exit(1);
}
echo "   ✅ All OAuth routes registered\n\n";

// Check User model
echo "5. Checking User model configuration...\n";
$user = new App\Models\User();
$fillable = $user->getFillable();

if (!in_array('google_id', $fillable) || !in_array('avatar', $fillable)) {
    echo "   ❌ User model missing google_id or avatar in fillable\n";
    exit(1);
}
echo "   ✅ User model configured correctly\n\n";

echo "=== ✅ All checks passed! Google OAuth is ready to use ===\n\n";
echo "Test URLs:\n";
echo "- Google Login: " . env('APP_URL') . "/auth/google\n";
echo "- Frontend: " . env('FRONTEND_URL') . "\n\n";
