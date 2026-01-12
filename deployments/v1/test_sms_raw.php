<?php
/**
 * Standalone SMS Tester
 * This bypasses Laravel to test the API directly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// YOUR CONFIG (Adjust if these are still placeholders)
$token = '227|EIMSSGYEhncx4oKy3Se3uSDLUQQF06e14GM4Rror'; // Found in your Filesta config
$sender_id = 'DIGIMART';
$admin_number = '94774665742';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Raw SMS Tester</title>
    <style>
        body { font-family: sans-serif; padding: 40px; line-height: 1.6; }
        pre { background: #eee; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Raw SMS API Tester</h1>";

if (isset($_POST['send'])) {
    $token = $_POST['token'];
    $number = $_POST['number'];
    $message = "Test Message from Standalone Script at " . date('H:i:s');
    
    echo "<h3>Testing with:</h3>";
    echo "Number: $number<br>";
    echo "Token: " . substr($token, 0, 10) . "...<br>";
    
    $ch = curl_init('https://dashboard.smsapi.lk/api/v3/sms/send');
    
    $data = [
        'recipient' => $number,
        'sender_id' => $sender_id,
        'type' => 'plain',
        'message' => $message
    ];
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<h3>API Response (Code $http_code):</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $result = json_decode($response, true);
    if ($http_code == 200 && isset($result['status']) && $result['status'] === 'success') {
        echo "<p class='success'>✅ API says SUCCESS! If you don't get the SMS, check your SMSAPI.LK balance or Sender ID approval.</p>";
    } else {
        echo "<p class='error'>❌ API failed. See the error above.</p>";
    }
}

?>
    <hr>
    <form method="POST">
        <p>Token:<br><input type="text" name="token" value="<?php echo $token; ?>" style="width: 100%; padding: 10px;"></p>
        <p>Number:<br><input type="text" name="number" value="<?php echo $admin_number; ?>" style="width: 100%; padding: 10px;"></p>
        <button type="submit" name="send" style="padding: 15px 30px; background: #6366f1; color: white; border: none; cursor: pointer;">Send Test SMS</button>
    </form>
</body>
</html>
