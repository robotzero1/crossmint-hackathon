<?php

require_once('stripe/init.php');
    
\Stripe\Stripe::setApiKey('sk_test_xyz');

try {
    // need to fiddle with this to use this
    // $customer = \Stripe\Customer::create([        
    //     'description' => 'Holiday Reservation',
    //     'name' => $contact,
    //     'email' => $contactEmail,
    // ]);  
  
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => '3000',
        'currency' => 'usd',
        'payment_method_types' => ['card'],
        'description' => 'NFT and Poster',
    ]);
} catch (Error $e) {
    error_log($e->getMessage());
    http_response_code(500);
}

echo json_encode(array('client_secret' => $paymentIntent->client_secret, 'paymentintent_id' => $paymentIntent->id));
