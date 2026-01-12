#!/usr/bin/env php
<?php

/**
 * Firebase REST API Configuration Verification Script
 * 
 * This script verifies that Firebase is configured to use REST API instead of gRPC.
 */

echo "=================================================\n";
echo "Firebase REST API Configuration Verification\n";
echo "=================================================\n\n";

// Check if gRPC extension is loaded
$grpcLoaded = extension_loaded('grpc');
echo "1. Checking gRPC Extension...\n";
echo "   Status: " . ($grpcLoaded ? "❌ LOADED (will use gRPC)" : "✓ NOT LOADED (will use REST)") . "\n\n";

// Check composer.json configuration
echo "2. Checking composer.json configuration...\n";
$composerJson = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
if (isset($composerJson['config']['platform']['ext-grpc'])) {
    $grpcConfig = $composerJson['config']['platform']['ext-grpc'];
    echo "   ext-grpc setting: " . ($grpcConfig === "0" || $grpcConfig === 0 ? "✓ 0 (REST mode)" : "❌ " . $grpcConfig) . "\n";
} else {
    echo "   ext-grpc setting: ⚠ NOT SET (will auto-detect)\n";
}
echo "\n";

// Check Firebase credentials file
echo "3. Checking Firebase credentials...\n";
$credentialsPath = __DIR__ . '/storage/app/firebase_credentials.json';
if (file_exists($credentialsPath)) {
    echo "   Credentials file: ✓ EXISTS\n";
    $credentials = json_decode(file_get_contents($credentialsPath), true);
    if (isset($credentials['project_id'])) {
        echo "   Project ID: " . $credentials['project_id'] . "\n";
    }
} else {
    echo "   Credentials file: ❌ NOT FOUND at $credentialsPath\n";
}
echo "\n";

// Check config/firebase.php
echo "4. Checking config/firebase.php...\n";
$firebaseConfig = __DIR__ . '/config/firebase.php';
if (file_exists($firebaseConfig)) {
    echo "   Config file: ✓ EXISTS\n";
} else {
    echo "   Config file: ❌ NOT FOUND\n";
}
echo "\n";

// Check .env file
echo "5. Checking .env configuration...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "   .env file: ✓ EXISTS\n";
    $envContent = file_get_contents($envFile);
    $hasFirebaseCredentials = strpos($envContent, 'FIREBASE_CREDENTIALS') !== false;
    echo "   FIREBASE_CREDENTIALS: " . ($hasFirebaseCredentials ? "✓ SET" : "⚠ NOT SET") . "\n";
} else {
    echo "   .env file: ❌ NOT FOUND\n";
    echo "   Run: cp .env.example .env\n";
}
echo "\n";

// Check required packages
echo "6. Checking required packages...\n";
$composerLock = json_decode(file_get_contents(__DIR__ . '/composer.lock'), true);
$packages = array_merge($composerLock['packages'] ?? [], $composerLock['packages-dev'] ?? []);

$requiredPackages = [
    'kreait/laravel-firebase' => false,
    'google/cloud-firestore' => false,
];

foreach ($packages as $package) {
    if (isset($requiredPackages[$package['name']])) {
        $requiredPackages[$package['name']] = $package['version'];
    }
}

foreach ($requiredPackages as $name => $version) {
    if ($version) {
        echo "   $name: ✓ $version\n";
    } else {
        echo "   $name: ❌ NOT INSTALLED\n";
    }
}
echo "\n";

// Summary
echo "=================================================\n";
echo "Summary\n";
echo "=================================================\n\n";

$hasGrpcConfig = isset($composerJson['config']['platform']['ext-grpc']) && 
                 ($composerJson['config']['platform']['ext-grpc'] === "0" || 
                  $composerJson['config']['platform']['ext-grpc'] === 0);

if ($hasGrpcConfig) {
    echo "✓ Configuration is CORRECT for REST API mode\n";
    echo "  The 'ext-grpc': '0' setting in composer.json forces REST API usage\n";
    if ($grpcLoaded) {
        echo "  Note: gRPC extension is installed but will be IGNORED by Composer\n";
        echo "  Firebase SDK will use REST API (HTTP/JSON) instead of gRPC\n";
    } else {
        echo "  Firebase will use REST API (HTTP/JSON) instead of gRPC\n";
    }
    echo "\n";
} elseif ($grpcLoaded) {
    echo "⚠ gRPC extension is loaded\n";
    echo "  Firebase will use gRPC instead of REST API\n";
    echo "  To force REST mode, add 'ext-grpc': '0' to composer.json config.platform\n\n";
} else {
    echo "✓ gRPC extension is NOT loaded\n";
    echo "  Firebase will automatically use REST API\n";
    echo "  You can optionally add 'ext-grpc': '0' to composer.json for explicit configuration\n\n";
}

echo "Next Steps:\n";
if (!file_exists($credentialsPath)) {
    echo "1. Place your Firebase credentials at: storage/app/firebase_credentials.json\n";
}
if (!file_exists($envFile)) {
    echo "2. Create .env file: cp .env.example .env\n";
}
echo "3. Run: composer install\n";
echo "4. Run: php artisan config:clear\n";
echo "5. Test connection: php artisan tinker\n";
echo "   Then run: Firebase::firestore()->database()->collection('users')->documents();\n";
echo "\n";

