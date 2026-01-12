<?php
/**
 * Supabase Helper Class
 * Handles database operations via REST API
 */

require_once 'config.php';

class SupabaseHelper {
    private $url;
    private $key;
    
    public function __construct() {
        $this->url = SUPABASE_URL;
        $this->key = SUPABASE_KEY;
    }
    
    /**
     * Record a payment
     */
    public function recordPayment($data) {
        if (!defined('SUPABASE_ENABLED') || !SUPABASE_ENABLED) {
            return false;
        }
        
        $endpoint = $this->url . '/rest/v1/payments';
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=minimal' // Don't need the inserted object back
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // 201 Created is success
        if ($http_code === 201) {
            return true;
        } else {
            // Log error
            $log_file = __DIR__ . '/payment_logs.txt';
            $log_entry = date('Y-m-d H:i:s') . " - Supabase Error: HTTP $http_code - $response - $error\n";
            file_put_contents($log_file, $log_entry, FILE_APPEND);
            return false;
        }
    }
}
?>
