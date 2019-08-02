<?php
require_once("../wp-load.php");
global $wpdb;

$profile_start_for_year =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*365);
$profile_start_for_month =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*30);

if(session_start())
{
    session_destroy();
}
session_start();


if(isset($_POST['sub_btn']))
{
$_SESSION['price'] = $price = $_POST['price'];
$_SESSION['plan_name'] = $plan_name = $_POST['plan_name'];
$_SESSION['period'] = $period = $_POST['period'];
$_SESSION['frequency'] = $frequency = $_POST['frequency'];
$_SESSION['desc'] = $desc = $_POST['desc'];
$_SESSION['payment_type'] = $paymentType = "Authorization";
$user_id = get_current_user_id();

// $_SESSION['total_cycle'] = $total_cycle;

if($_SESSION['frequency'] == '1' && $_SESSION['period'] == 'Month')
{
    $_SESSION['profile_start_date'] = $profile_start_for_month;
}
else if($_SESSION['frequency'] == '1' && $_SESSION['period'] == 'Year')
{
    $_SESSION['profile_start_date'] = $profile_start_for_year;
// $_SESSION['next_payment_date'] = date('Y-m-d\T00:00:00\Z',time()+86400*365);
}
// $_SESSION['init_amt'] = $init_amt;
/*    $price = 82;
$period = 'Month';
$frequency = 1;
$total_cycle = 3;
// $profile_start_date = gmdate("Y-m-d\TH:i:s\Z");
$profile_start_date = '2016-11-23T00:00:00Z';*/

    // $price = $_POST['price'];
// require_once('dbConfig.php');
// print_r($_SESSION);die;
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
 
    'PAYMENTREQUEST_0_AMT' => $price,
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_PAYMENTACTION' => $paymentType,
    'PAYMENTREQUEST_0_ITEMAMT' => $price,
 
    'L_PAYMENTREQUEST_0_NAME0' => $plan_name,
    'L_PAYMENTREQUEST_0_DESC0' => $desc,
    'L_PAYMENTREQUEST_0_QTY0' => 1,
    'L_PAYMENTREQUEST_0_AMT0' => $price,
    'L_BILLINGTYPE0' => 'RecurringPayments',
    'L_BILLINGAGREEMENTDESCRIPTION0' =>$desc,
 
    'CANCELURL' => site_url().'/caveman-membership',
    'RETURNURL' => site_url().'/my-account'
)));
 
$response = curl_exec($curl);
 
curl_close($curl);
 
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}
if (isset($nvp['ACK']) && $nvp['ACK'] == 'Success') {
    $query = array(
        'cmd'    => '_express-checkout',
        'token'  => $nvp['TOKEN']
    );
   $qry = "INSERT INTO token_details (token, user_id, is_cancelled, is_activated)
	VALUES ('".$nvp['TOKEN']."','".$user_id."', '0', '1')";

          
        $res_query = $wpdb->query($qry);

		if ($res_query) {
		    $redirectURL = sprintf('https://www.sandbox.paypal.com/cgi-bin/webscr?%s', http_build_query($query));
		    header('Location: ' . $redirectURL);
		} else {
		    echo "error while payment";
		}

} 
else
{
    echo 'error_code '.$nvp['L_ERRORCODE0'].' Error - '.$nvp['L_LONGMESSAGE0'];
}

}