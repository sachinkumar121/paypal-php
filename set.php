<?php
require_once('dbConfig.php');

// date_default_timezone_set("UTC");		
// echo date("Y-m-d\TH:i:s\Z");die;
$curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
    'PWD' => '9PZCLK395J63RXPQ',
    'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
 
    'METHOD' => 'SetExpressCheckout',
    'VERSION' => '108',
    'LOCALECODE' => 'en-US',
 
    'PAYMENTREQUEST_0_AMT' => 110,
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
    'PAYMENTREQUEST_0_ITEMAMT' => 110,
 
    'L_PAYMENTREQUEST_0_NAME0' => 'Monthly Subscription',
    'L_PAYMENTREQUEST_0_DESC0' => 'Here the monthly subscription',
    'L_PAYMENTREQUEST_0_QTY0' => 1,
    'L_PAYMENTREQUEST_0_AMT0' => 110,
    'L_BILLINGTYPE0' => 'RecurringPayments',
    'L_BILLINGAGREEMENTDESCRIPTION0' => 'Agree for 1 month',
 
    'CANCELURL' => 'http://localhost/paypal/cancel.php',
    'RETURNURL' => 'http://localhost/paypal/success.php'
)));
 
$response = curl_exec($curl);
 
curl_close($curl);
 
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}
// print_r($nvp);die;
if (isset($nvp['ACK']) && $nvp['ACK'] == 'Success') {
    $query = array(
        'cmd'    => '_express-checkout',
        'token'  => $nvp['TOKEN']
    );
    $sql = "INSERT INTO token_details (token, user_id, is_cancelled, is_activated)
	VALUES ('".$nvp['TOKEN']."', '121', '0', '1')";

		if ($conn->query($sql) === TRUE) {
		    // echo "New record created successfully";
		    $conn->close();
		    $redirectURL = sprintf('https://www.sandbox.paypal.com/cgi-bin/webscr?%s', http_build_query($query));
		    header('Location: ' . $redirectURL);
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}


} else {
 } 