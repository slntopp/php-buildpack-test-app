<?
$orderid = 'XFRX8M92MB200310GUEST000P01';
$namegateway = 'PayU';
$invoiceid = '144';
$atoken = '3f7b0808-7cb5-4d91-a335-003dd6221135';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://secure.payu.com/api/v2_1/orders/".$orderid."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$atoken.""
));

$response = curl_exec($ch);
curl_close($ch);

$myVar = json_decode($response);
print_r($myVar);