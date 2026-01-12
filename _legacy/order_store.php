<?php
// order_store.php - Atomic file-based persistence for order context with proper locking

function saveOrderContext($order_id, $data) {
    $file = __DIR__ . '/pending_orders.json';
    
    // Create file if doesn't exist
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    
    // Open file with exclusive lock to prevent race conditions
    $fp = fopen($file, 'r+');
    if (!$fp) {
        error_log("Failed to open pending_orders.json");
        return false;
    }
    
    // Acquire exclusive lock (blocks until lock is obtained)
    if (flock($fp, LOCK_EX)) {
        $content = stream_get_contents($fp);
        $orders = json_decode($content, true) ?: [];
        
        // Add new data (or update)
        $orders[$order_id] = [
            'data' => $data,
            'timestamp' => time()
        ];
        
        // Cleanup old orders (>24 hours) to keep file small
        foreach ($orders as $key => $val) {
            if (time() - $val['timestamp'] > 86400) {
                unset($orders[$key]);
            }
        }
        
        // Write back to file atomically
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($orders, JSON_PRETTY_PRINT));
        
        // Release lock
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    } else {
        fclose($fp);
        error_log("Failed to acquire lock on pending_orders.json");
        return false;
    }
}

function getOrderContext($order_id) {
    $file = __DIR__ . '/pending_orders.json';
    if (!file_exists($file)) {
        return null;
    }
    
    $content = file_get_contents($file);
    $orders = json_decode($content, true) ?: [];
    
    return isset($orders[$order_id]) ? $orders[$order_id]['data'] : null;
}

function deleteOrderContext($order_id) {
    // Optional: We might want to keep it briefly for page reloads
    // For now, let's keep it until cleanup to allow page reloads
    return;
}
?>
