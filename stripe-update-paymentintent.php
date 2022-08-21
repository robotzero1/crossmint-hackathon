<?php
require_once('stripe/init.php');
    
\Stripe\Stripe::setApiKey('sk_test_xyz');

$orderData = json_decode(file_get_contents('php://input'), true);

try {
    
    $jsonOrderData = json_encode($orderData);
    
    $paymentIntentUpdate = \Stripe\PaymentIntent::update(
        $orderData['paymentintentkey'],
        ['metadata' => ['order_data' => $jsonOrderData],
        'statement_descriptor' => 'Robot Moda Loves You',
        'receipt_email' => $orderData['email'],
        ]
    );
} catch (Error $e) {
    error_log($e->getMessage());
    http_response_code(500);
}

echo $paymentIntentUpdate;
