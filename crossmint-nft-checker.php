$crossMintClientSecret = 'sk_test.xyz';
$crossMintProjectID = '123';

$url = 'https://staging.crossmint.io/api/2022-06-09/collections/default/nfts/'.$_GET['nftid'];


$fieldsJSON = json_encode($fields);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
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
    echo json_encode(array('image' => $json['metadata']['image'], 'status' => $json['onChain']['status'], 'tokenid' => $json['onChain']['tokenId']));
}
