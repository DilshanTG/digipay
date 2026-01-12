function paymentGateway() {
    var xhttp = new XMLHttpRequest();
    
    // When the state changes, check if the response is ready
    xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            // Parse the JSON response from payment_process.php
            var obj = JSON.parse(xhttp.responseText);

            // Payment completed or failed handler
            payhere.onCompleted = function onCompleted(orderId) {
                console.log("Payment completed. OrderID:" + orderId);
                alert("Payment completed. Order ID: " + orderId);
            };

            // Payment window closed handler
            payhere.onDismissed = function onDismissed() {
                console.log("Payment dismissed");
                alert("Payment dismissed.");
            };

            // Error handler for the payment
            payhere.onError = function onError(error) {
                console.log("Error:" + error);
                alert("Payment error: " + error);
            };

            // Prepare the payment object using the response from the PHP file
            var payment = {
                "sandbox": true, // Set to false in live environment
                "merchant_id": obj["merchant_id"], 
                "return_url": "http://localhost/payhere_config/", 
                "cancel_url": "http://localhost/payhere_config/", 
                "notify_url": "http://sample.com/notify", 
                "order_id": obj["order_id"], 
                "items": "Door bell wireless", 
                "amount": obj["amount"], 
                "currency": obj["currency"], 
                "hash": obj["hash"], 
                "first_name": obj["first_name"], 
                "last_name": obj["last_name"], 
                "email": obj["email"], 
                "phone": obj["phone"], 
                "address": obj["address"], 
                "city": obj["city"], 
                "country": "Sri Lanka", 
            };

            // Start the payment process
            payhere.startPayment(payment);
        }
    };

    // Send a GET request to the payment_process.php to fetch payment details
    xhttp.open("GET", "payment_process.php", true);
    xhttp.send();
}