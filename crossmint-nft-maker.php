<?php

$crossMintClientSecret = 'sk_test.xyz';
$crossMintProjectID = '123';

$url = 'https://staging.crossmint.io/api/2022-06-09/collections/default/nfts';
$imageBase = 'https://domain.com/images/';

$databaseRecord = 'minted/nft_minted_id_'.$_GET['nftid']; // "database"
$transactionJSON = file_get_contents($databaseRecord);
$transactionArray =  json_decode($transactionJSON, TRUE);

// $transactionArray['paymentintentkey']; could be used for transaction check

// Recepient is wallet address or email?
// TODO check wallet address valid and default to email?
$recipient = $transactionArray['wallet-address'] !== "" ? 'poly:'.$transactionArray['wallet-address'] : 'email:'.$transactionArray['email'].':poly';


$fields['metadata']['name'] = 'NFT ID '.$_GET['nftid'];
$fields['metadata']['image'] = $imageBase.'image ('.$_GET['nftid'].').png';
$fields['metadata']['description'] = 'Crossmint NFT Hackathon';
$fields['recipient'] = $recipient;

$fieldsJSON = json_encode($fields);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $fieldsJSON,
    CURLOPT_HTTPHEADER => [
        "content-type: application/json", 
        "x-client-secret: $crossMintClientSecret",    
        "x-project-id: $crossMintProjectID",
    ],        
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $json = json_decode($response, true);
    echo json_encode(array('crossmint_mint_id' => $json['id'], 'recipient' => $recipient));
}
