<?php
require_once('stripe/init.php');
    
\Stripe\Stripe::setApiKey('sk_test_xyz');


// This is your Stripe CLI webhook secret for testing your endpoint locally.
$endpoint_secret = 'whsec_abc';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

// Handle the event
switch ($event->type) {
  case 'charge.succeeded':

    // Write order details to database. Basic file system used for demo.

    // need to redo the way the metadata is sent
    $metadata = stripslashes($event->data->object->metadata);
    
    // spaghetti time. Look away now.
    $metadata = str_replace('    "order_data": "', '', $metadata);
    $metadata = str_replace('}"', '}', $metadata);
    $metadata = ltrim($metadata,'StripeStripeObject JSON: {');
    $metadata = rtrim($metadata,'}');
    $metadata = rtrim($metadata);    
    $metadataArray = json_decode($metadata, TRUE);
    
    
    // create a file (database entry) for the purchased NFT
    $nft = $metadataArray['nft'];
    $file = 'minted/nft_minted_id_'.$nft;
    // Add customers address
    //file_put_contents($file, var_export($metadataArray, true));
    file_put_contents($file, $metadata); // lets just keep it as JSON for now
    
    // file is now ready for polling JS in Mint and Print functions
    
    
  // ... handle other event types
  // error_log($metadata, 3, "/home/firenet/public_html/crossmint/webhook.log");

  default:
    echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
