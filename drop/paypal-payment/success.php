<?php
// require_once('dbConfig.php');
require_once("../wp-load.php");
$payer = '';
$sql = "SELECT token, is_cancelled, is_activated FROM token_details WHERE user_id=121 and token='".$_GET['token']."'";
echo $sql;die;
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 1) {
    // output data of each row
   $row = mysqli_fetch_assoc($result);
   if($row["is_cancelled"] == '0' && $row["is_activated"] == '1' )
   {
        // echo "token: " . $row["token"]. " - is_cancelled: " . $row["is_cancelled"];
   		if($_GET['token'] !== $row["token"])
   		{
   			echo "unauth token";
   			return false;
   		}
        $curl = curl_init();
 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
		    'USER' => 'sukant_api1.mobilyte.com',
		    'PWD' => '9PZCLK395J63RXPQ',
		    'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',		 
		    'METHOD' => 'GetExpressCheckoutDetails',
		    'VERSION' => '108',
		 
		    'TOKEN' => $row["token"]
		)));
		 
		$response =  curl_exec($curl);
		 
		curl_close($curl);
		 
		$nvp = array();
		 
		if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
		    foreach ($matches['name'] as $offset => $name) {
		        $nvp[$name] = urldecode($matches['value'][$offset]);
		    }
		}
		// print_r($nvp);die;
		$payer = $nvp['PAYERID'];
		 if($nvp['PAYERID'] !== $_GET['PayerID'])
		 {
		 	echo 'unauth payer id';
		 }
		 else
		 {
		 	goto createProfile;
		 }
		// print_r($nvp);
   }
   else
   {
   	echo 'already cancel';
   }
    
} else {
    echo "unauth wrong details";
}

createProfile:
// echo $payer;die;
	$sql_update_token = "UPDATE token_details set is_activated = '0' WHERE user_id=121 and token='".$row['token']."'";
	// echo $sql;die;
	$result_update_token = mysqli_query($conn, $sql_update_token);

	$curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
	'PWD' => '9PZCLK395J63RXPQ',
	'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
 
    'METHOD' => 'CreateRecurringPaymentsProfile',
    'VERSION' => '108',
    'LOCALECODE' => 'en-US',
 
    'TOKEN' => $row['token'],
    'PayerID' => $payer,
 
    'PROFILESTARTDATE' => '2016-11-21T16:00:00Z',
    'DESC' => 'Agree for 1 month',
    'BILLINGPERIOD' => 'Month',
    'BILLINGFREQUENCY' => '1',
    'AMT' => 100,
    'CURRENCYCODE' => 'USD',
    'COUNTRYCODE' => 'US',
    'MAXFAILEDPAYMENTS' => 3
)));
 
$response = curl_exec($curl);
 
curl_close($curl);
 
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}
 
// print_r($nvp);
if (isset($nvp['ACK']) && $nvp['ACK'] == 'Success') {
$sql_trans_qry = "INSERT INTO transaction_details (token, user_id, profile_id, profile_status, correlation_id, 
		payer_id, item_name, item_desc, profile_start_date, next_payment_date, 
		frequency, period, amt, currency_code, country_code,created_at )
	VALUES ('".$row['token']."', '121', '".$nvp['PROFILEID']."', '".$nvp['PROFILESTATUS']."', '".$nvp['CORRELATIONID']."',
			'$payer', 'subscribe', 'Agree for 1 month', '2016-11-21T16:00:00Z', '2016-12-21T16:00:00Z',
			'1', 'month', 100, 'USD', 'US', '".$nvp['TIMESTAMP']."'
		)";
		// echo $sql_trans_qry;
		echo 'successfully created';
		echo '<a href="http://localhost/paypal/cancelSub.php?profile_id='.$nvp['PROFILEID'].'">cancel subscribe</a>';
		echo '<a href="http://localhost/paypal/Reactivate.php?profile_id='.$nvp['PROFILEID'].'">Reactivate subscribe</a>';
		echo '<a href="http://localhost/paypal/getDetails.php?profile_id='.$nvp['PROFILEID'].'">Get Details subscribe</a>';
		echo '<a href="http://localhost/paypal/Suspend.php?profile_id='.$nvp['PROFILEID'].'">Suspand subscribe</a>';
$result_trans_qry = mysqli_query($conn, $sql_trans_qry);
	}
	else
	{
		echo 'error_code '.$nvp['L_ERRORCODE0'].' Error - '.$nvp['L_LONGMESSAGE0'];
	}

/*	$curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
	'PWD' => '9PZCLK395J63RXPQ',
	'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
 
    'METHOD' => 'UpdateRecurringPaymentsProfile',
    'VERSION' => '108',
    'LOCALECODE' => 'en-US',
    'PROFILEID' => $nvp['PROFILEID']
    )));
 
$response = curl_exec($curl);
 
curl_close($curl);
 
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}

print_r($nvp);	*/
die;
// print_r($result);die;
