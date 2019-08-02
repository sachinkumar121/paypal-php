<?php
// echo  gmdate("Y-m-d\TH:i:s\Z");die;
require_once('dbConfig.php');
$price = 155.66;
$version = '95';
$period = 'Year';
$frequency = 1;
$total_cycle = 0;
// $profile_start_date = gmdate("Y-m-d\T0:0:0\Z");
$profile_start_for_year =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*365);
$profile_start_for_month =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*30);
// $paymentType = "Sale";
$paymentType = "Authorization";
if(session_start())
{
    session_destroy();
}
session_start();
$_SESSION['price'] = $price;
$_SESSION['period'] = $period;
$_SESSION['frequency'] = $frequency;
$_SESSION['total_cycle'] = $total_cycle;
$_SESSION['profile_start_date'] = $profile_start_date;
// $_SESSION['init_amt'] = $init_amt;
// print_r($_SESSION);die;
#$paymentType = "Order";
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
    'VERSION' => $version,
    'LOCALECODE' => 'en-US',
    'PAYMENTACTION' => $paymentType,
    'AMT' => $price,
    'CURRENCYCODE'=>'USD',

    'PAYMENTREQUEST_0_AMT' =>$price,
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_PAYMENTACTION' => $paymentType,
    // 'PAYMENTREQUEST_0_ITEMAMT' => 100,
 
    'L_PAYMENTREQUEST_0_NAME0' => 'Monthly Subscription',
    'L_PAYMENTREQUEST_0_DESC0' => 'Here the monthly subscription',
    // 'L_PAYMENTREQUEST_0_QTY0' => 1,
    'L_PAYMENTREQUEST_0_AMT0' => $price,

 
    
    'L_BILLINGTYPE0' => 'RecurringPayments',
    'L_BILLINGAGREEMENTDESCRIPTION0' => 'Agree for monthly subscription',
 
    'CANCELURL' => 'http://localhost/paypal-php/cancel.php',
    'RETURNURL' => 'http://localhost/paypal-php/success_init.php'

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