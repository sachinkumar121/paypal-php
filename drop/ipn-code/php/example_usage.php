<?php 
// $payment_status = '1';

require('PaypalIPN.php');
require_once("../../wp-load.php");
global $wpdb;
use PaypalIPN;


$ipn = new PayPalIPN();

// Use the sandbox endpoint during testing.
$ipn->useSandbox();
$verified = $ipn->verifyIPN();
if ($verified) {

	if($_POST['txn_type'] == 'recurring_payment')
	{
		/*$qry = "UPDATE transaction_details SET transc_id = '".$_POST['txn_id']."',payment_status = ".$_POST['payment_status'].",payment_date = ".$_POST['payment_date'].", next_payment_date = ".$_POST['next_payment_date'].", profile_status = ".$_POST['profile_status'].", time_created = ".$_POST['time_created']." where payer_id = '".$_POST['payer_id']"' AND profile_id = ".$_POST['recurring_payment_id'];*/
		$qry = "UPDATE transaction_details SET transc_id = '".$_POST['txn_id']."',payment_status = '".$_POST['payment_status']."',payment_date_full_format = '".$_POST['payment_date']."', next_payment_date_full_format = '".$_POST['next_payment_date']."', profile_status = '".$_POST['profile_status']."', time_created_full_format = '".$_POST['time_created']."' where payer_id = '".$_POST['payer_id']."' AND profile_id = '".$_POST['recurring_payment_id']."'";
		$res_query = $wpdb->query($qry);
		
	}
	if($_POST['txn_type'] == 'express_checkout')
	{
		$qry = "UPDATE transaction_details SET transc_id = '".$_POST['txn_id']."',payment_status = '".$_POST['payment_status']."',payment_date_full_format = '".$_POST['payment_date']."', where payer_id = '".$_POST['payer_id']."' AND parent_txn_id = '".$_POST['parent_txn_id']."'";
		$res_query = $wpdb->query($qry);
		
	}
	if($_POST['txn_type'] == 'recurring_payment_profile_cancel')
	{
		/*$qry = "UPDATE transaction_details SET transc_id = '".$_POST['txn_id']."',payment_status = ".$_POST['payment_status'].",payment_date = ".$_POST['payment_date'].", next_payment_date = ".$_POST['next_payment_date'].", profile_status = ".$_POST['profile_status'].", time_created = ".$_POST['time_created']." where payer_id = '".$_POST['payer_id']"' AND profile_id = ".$_POST['recurring_payment_id'];*/
		$qry = "UPDATE transaction_details SET profile_status = '".$_POST['profile_status']."' where payer_id = '".$_POST['payer_id']."' AND profile_id = '".$_POST['recurring_payment_id']."'";
		$res_query = $wpdb->query($qry);
		
	}


	/*$item_name = $_POST['item_name'];
  	$item_number = $_POST['item_number'];
  	$payment_status = $_POST['payment_status'];
  	$payment_amount = $_POST['mc_gross'];
  	$payment_currency = $_POST['mc_currency'];
  	$txn_id = $_POST['txn_id'];
  	$receiver_email = $_POST['receiver_email'];
  	$payer_email = $_POST['payer_email'];
  // IPN message values depend upon the type of notification sent.
  // To loop through the &_POST array and print the NV pairs to the screen:
  foreach($_POST as $key => $value) {
    echo $key . " = " . $value . "<br>";
  }*/
	/*print_r($_POST);die;
	$payment_status = $_POST['payment_status'];
	$qry = "INSERT INTO token_details (token, user_id, is_cancelled, is_activated, details)
	VALUES ('111', '121', '0', '1', '".$payment_status."')";
	$res_query = $wpdb->query($qry); */

    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */
}

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
