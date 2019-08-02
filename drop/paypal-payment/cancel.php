<?php
require_once('dbConfig.php');

if(isset($_GET['profile_id']))
{
		$curl = curl_init();
 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
		    'USER' => 'sukant_api1.mobilyte.com',
		    'PWD' => '9PZCLK395J63RXPQ',
		    'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',		 
		    'METHOD' => 'GetRecurringPaymentsProfileDetails',
		    'VERSION' => '108',
		 	// 'ACTION' => 'Reactivate',
		 	// 'NOTE' => 'disable no 1',
		    'PROFILEID' => $_GET['profile_id']
		)));
		 
		$response =  curl_exec($curl);
		 
		curl_close($curl);
		 
		$nvp = array();
		if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
		    foreach ($matches['name'] as $offset => $name) {
		        $nvp[$name] = urldecode($matches['value'][$offset]);
		    }
		}
		print_r($nvp);die;
}
$sql = "SELECT token, is_cancelled, is_activated FROM token_details WHERE user_id=121 and token='".$_GET['token']."'";
// echo $sql;die;
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
   		$sql_update_token = "UPDATE token_details set is_activated = '0', is_cancelled = '1' WHERE user_id=121 and token='".$row['token']."'";
		// echo $sql;die;
		$result_update_token = mysqli_query($conn, $sql_update_token);

		echo 'cancel successfully';
   	}
   	else
   	{
   		echo 'already cancel';
   	}
   }
   else
   {
   	echo 'wrong token';
   }

