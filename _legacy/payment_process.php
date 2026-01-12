<?php

$amount = 3000; // Payment amount
$merchant_id = ""; // Replace with your PayHere Merchant ID
$order_id = uniqid(); // Unique order ID for this transaction
$currency = "LKR"; // Payment currency
$merchant_secret = ""; // Replace with your Merchant Secret

// Generating the hash value for payment security
$hash = strtoupper(
    md5(
        $merchant_id . 
        $order_id . 
        number_format($amount, 2, '.', '') . 
        $currency . 
        strtoupper(md5($merchant_secret))
    )
);

// Creating the array for payment details
$array = [];
$array["first_name"] = "Saman";
$array["last_name"] = "Kumara";
$array["email"] = "samankumara@gmail.com";
$array["phone"] = "078922033748";
$array["address"] = "102, Colombo, Sri Lanka";
$array["city"] = "Colombo";
$array["amount"] = $amount;
$array["merchant_id"] = $merchant_id;
$array["order_id"] = $order_id;
$array["currency"] = $currency;
$array["hash"] = $hash;

// Encoding the array to JSON format
$jsonObj = json_encode($array);
echo $jsonObj;

?>