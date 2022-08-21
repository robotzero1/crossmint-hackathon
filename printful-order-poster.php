<?php
$printfullApiKey = "xyz";


$databaseRecord = 'minted/nft_minted_id_'.$_GET['nftid']; // "database"
$transactionJSON = file_get_contents($databaseRecord);
$transactionArray =  json_decode($transactionJSON, TRUE);

// definitely need to check this is a real order using Stripe payment intent or client secret from the 'database' for this transaction. Also mark 'database' as done to prevent reorder.


// this is probably bad but OK for a demo maybe?
$json = '
{
  "external_id": "NFT '.$transactionArray['nft'].'aaaaa",
  "recipient": {
    "name": "'.$transactionArray['name'].'",
    "company": "'.$transactionArray['company'].'",
    "address1": "'.$transactionArray['address1'].'",
    "address2": "'.$transactionArray['address2'].'",
    "city": "'.$transactionArray['city'].'",
    "state_code": "'.$transactionArray['state_code'].'",
    "state_name": "'.$transactionArray['state_name'].'",
    "country_code": "'.$transactionArray['country_code'].'",
    "country_name": "'.$transactionArray['country_name'].'",
    "zip": "'.$transactionArray['zip'].'",
    "email": "'.$transactionArray['email'].'"
  },
  "items": [
    {
      "id": 282489759,
      "external_id": "poster",
      "variant_id": 1,
      "quantity": 1,
      "name": "Poster",
      "files": [
        {
            "id": 456231094
        }
        ]
    }
  ]
}
';


$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.printful.com/orders",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => [
        "content-type: application/json",    
        "Authorization: Bearer $printfullApiKey",
    ],        
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $json = json_decode($response, true);
    echo $json['result']['status'];
}
