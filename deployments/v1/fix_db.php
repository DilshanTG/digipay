<?php
/**
 * DigiMart Pay - Database Fix Script
 * Visit this at yourdomain.com/fix_db.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$autoload = __DIR__ . '/vendor/autoload.php';
$bootstrap = __DIR__ . '/bootstrap/app.php';

if (!file_exists($autoload)) {
    die("ERROR: 'vendor/autoload.php' not found. Did you upload the 'vendor' folder completely?");
}

if (!file_exists($bootstrap)) {
    die("ERROR: 'bootstrap/app.php' not found. Check your file structure.");
}

require $autoload;
$app = require_once $bootstrap;
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain');

echo "--- DigiMart Pay Database Repair ---\n\n";

try {
    $tableName = 'merchants';
    $columns = ['return_url', 'cancel_url', 'notify_url'];
    
    echo "Checking table: $tableName...\n";
    
    if (!Schema::hasTable($tableName)) {
        die("ERROR: Table '$tableName' does not exist!\n");
    }

    $missing = [];
    foreach ($columns as $column) {
        if (!Schema::hasColumn($tableName, $column)) {
            $missing[] = $column;
            echo "[-] Column '$column' is MISSING.\n";
        } else {
            echo "[+] Column '$column' already exists.\n";
        }
    }

    if (empty($missing)) {
        echo "\n✅ Success: All columns are already present! No changes needed.\n";
    } else {
        echo "\nAttempting to add missing columns...\n";
        
        Schema::table($tableName, function (Blueprint $table) use ($missing) {
            foreach ($missing as $col) {
                $table->string($col)->nullable();
            }
        });
        
        echo "✅ Missing columns added successfully!\n";
    }

    // --- Payments Table Fix ---
    $tableName = 'payments';
    $columns = ['notify_url'];
    
    echo "\nChecking table: $tableName...\n";
    
    if (Schema::hasTable($tableName)) {
        foreach ($columns as $column) {
            if (!Schema::hasColumn($tableName, $column)) {
                echo "[-] Column '$column' is MISSING in $tableName. Adding...\n";
                Schema::table($tableName, function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable()->after('redirect_url');
                });
                echo "[+] Column '$column' added.\n";
            } else {
                echo "[+] Column '$column' already exists in $tableName.\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n--- Process Finished ---\n";
echo "Please DELETE this file (fix_db.php) now for security.\n";
