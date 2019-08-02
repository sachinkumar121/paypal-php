<?php
$trans_id = $_GET['transc'];
$curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
	'PWD' => '9PZCLK395J63RXPQ',
	'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
    'METHOD' => 'GetTransactionDetails',
    'VERSION' => '95',
    'LOCALECODE' => 'en-US', 
    'TRANSACTIONID' => $trans_id

)));
 
$response = curl_exec($curl);
 
curl_close($curl);	
// print_r($nvp);
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}

 
print_r($nvp);die;