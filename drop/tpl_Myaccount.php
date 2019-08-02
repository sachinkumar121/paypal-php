<?php 
/*Template name:My Account*/

get_header();
// date_default_timezone_set('UTC');
// $profile_start_for_year =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*365);
// $profile_start_for_month =  gmdate('Y-m-d\T00:00:00\Z',time()+86400*30);

if(isset($_GET['token']) && isset($_GET['PayerID']))
{
// echo 'h';
// print_r($_SESSION);
// die;
// code for payment handle
/*$after_one_m = date('Y-m-d\TH:i:s\Z',time()+86400*30);
$after_one_y = date('Y-m-d\TH:i:s\Z',time()+86400*365);*/

// /$_SESSION['profile_start_date'] = $profile_start_date = gmdate("Y-m-d\T0:0:0\Z");
global $wpdb;
$user_id = get_current_user_id();
$payer = '';
$sql = "SELECT token, is_cancelled, is_activated FROM token_details WHERE user_id=".$user_id." and token='".$_GET['token']."'";
// echo $sql;die;
$row = $wpdb->get_row($sql,OBJECT);

// $result = mysqli_query($conn, $sql);
  // print_r($result);
if (count($row) == 1) {
// die;
    // output data of each row
   // $row = mysqli_fetch_assoc($result);
   if($row->is_cancelled == '0' && $row->is_activated== '1' )
   {
        // echo "token: " . $row["token"]. " - is_cancelled: " . $row["is_cancelled"];
      if($_GET['token'] !== $row->token)
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
     
        'TOKEN' => $row->token
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
  $sql_update_token = "UPDATE token_details set is_activated = '0' WHERE user_id='".$user_id."' and token='".$row->token."'";
  // ========== get auth id==================
  $curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
  'PWD' => '9PZCLK395J63RXPQ',
  'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
 
    'METHOD' => 'DoExpressCheckoutPayment',
    'VERSION' => '108',
    'LOCALECODE' => 'en-US',
    'AMT' => $_SESSION['price'],
 
    'TOKEN' => $row->token,
    'CURRENCYCODE' => 'USD',
    'PayerID' => $payer,
    'PAYMENTACTION' => $_SESSION['payment_type'],
 
   'L_BILLINGTYPE0' => 'RecurringPayments',
    'L_BILLINGAGREEMENTDESCRIPTION0' => $_SESSION['desc']



)));
 
$response = curl_exec($curl);
 
curl_close($curl);
$nvp = array();
 
if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
    foreach ($matches['name'] as $offset => $name) {
        $nvp[$name] = urldecode($matches['value'][$offset]);
    }
}
$auth_id = $nvp['PAYMENTINFO_0_TRANSACTIONID'];
// echo $auth_id;die;
$curl = curl_init();
 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
    'USER' => 'sukant_api1.mobilyte.com',
  'PWD' => '9PZCLK395J63RXPQ',
  'SIGNATURE' => 'A1eRIOfCz42bySEn-ogX.K0yYssjAT2HPxhA0eNcmRT2YL7vGIG18weK',
 
    'METHOD' => 'DoCapture',
    'VERSION' => '108',
    'LOCALECODE' => 'en-US',
    'AMT' => $_SESSION['price'],
 
    'AUTHORIZATIONID' => $auth_id,
    'CURRENCYCODE' => 'USD',
 
   'COMPLETETYPE' => 'Complete'

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
$trans_id = $nvp['TRANSACTIONID'];
  // ========== end of auth id =============

  // ======= get parent_transc_id ===========
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
$parent_txn_id =  $nvp['PARENTTRANSACTIONID'];
$transc_id =  $nvp['TRANSACTIONID'];


  // ============ end code here ============
  $result_update_token = $wpdb->query($sql_update_token);

  // $result_update_token = mysqli_query($conn, $sql_update_token);

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
 
    'TOKEN' => $row->token,
    'PayerID' => $payer,
 
    'PROFILESTARTDATE' => $_SESSION['profile_start_date'],
    'DESC' => $_SESSION['desc'],
    'BILLINGPERIOD' => $_SESSION['period'],
    'BILLINGFREQUENCY' => $_SESSION['frequency'],
    'AMT' => $_SESSION['price'],
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
 
// print_r($nvp);die;
if (isset($nvp['ACK']) && $nvp['ACK'] == 'Success') {

$sql_trans_qry = "INSERT INTO transaction_details (transc_id, parent_txn_id, token, user_id, profile_id, profile_status, correlation_id, 
    payer_id, item_name, item_desc, profile_start_date, next_payment_date, 
    frequency, period, amt, currency_code, country_code,created_at )
  VALUES ('".$transc_id."','".$parent_txn_id."','".$row->token."', '".$user_id."', '".$nvp['PROFILEID']."','".$nvp['PROFILESTATUS']."', '".$nvp['CORRELATIONID']."',
      '$payer','".$_SESSION['plan_name']."', '".$_SESSION['desc']."', '".gmdate('Y-m-d\T00:00:00\Z',time())."', '".$_SESSION['profile_start_date']."',
      '".$_SESSION['frequency']."', '".$_SESSION['period']."' , '".$_SESSION['price']."' , 'USD', 'US', '".$nvp['TIMESTAMP']."'
    )";
    // echo $sql_trans_qry;die;
$result_trans_qry = $wpdb->query($sql_trans_qry);
    echo 'successfully created';

    echo '<a href="'.site_url().'/paypal-payment/cancelSub.php?profile_id='.$nvp['PROFILEID'].'">cancel subscribe</a>';
    echo '<a href="'.site_url().'/paypal-payment/getDetails.php?profile_id='.$nvp['PROFILEID'].'">Get Details subscribe</a>';

    echo '<a href="'.site_url().'/paypal-payment/Reactivate.php?profile_id='.$nvp['PROFILEID'].'">Reactivate subscribe</a>';
    echo '<a href="'.site_url().'/paypal-payment/Suspend.php?profile_id='.$nvp['PROFILEID'].'">Suspand subscribe</a>';
// $result_trans_qry = mysqli_query($conn, $sql_trans_qry);
  }
  else
  {
    echo 'error_code '.$nvp['L_ERRORCODE0'].' Error - '.$nvp['L_LONGMESSAGE0'];
  }
}
  // end of code for payment handle
?>


<section class="conatact-top">
  <div class="container">
  <div class="row">
  
  <div class="col-xs-12">
  
   <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <h2 class="contact-gift-title "><?php the_title(); ?></h2>
            <p class="contact-gift-text"><?php echo get_the_content($post->ID ); ?></p>
            <?php endwhile; endif; ?>
  
  </div>
  
  </div>
  
  </div>
  
  
  </section>
  <section class="about-mid-section">
 
   
   <div class="clearfix"></div>
    <div class="container">
    
    <div class="cart-sec">
    
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
         <div class="row">



  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  
   <thead> <span class="package-title1">Packages</span>
    <span><a href="#" class="btn btn-add-new btn-sm pull-right"><b>+</b> Add new package</a></span>
  <div class="cart-table-responsive">
   <table class="table table-striped custab table-responsive" >
   


        <tr>
            <th>Sr No.</th>
            <th>Package Name</th>
            <th>Package Type</th>
            <th>Expiry</th>
            <th>Price</th>
            <th ></th>
        </tr>
    </thead>
            <tr>
                <td>1</td>
                <td>Stone Age Membership </td>
                <td>Monthly</td>
                 <td>00-nov-2016</td>
                <td>$9.99</td>
                <td class="text-center"> <a href="#" class="btn btn-danger btn-xs"><span class=" fa fa-trash "></span></a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Unlocking Your Optimal Ancestral Blueprint Online Course</td>
                <td>Nine Months</td>
                  <td>00-nov-2016</td>
                <td>$199</td>
                 <td class="text-center"> <a href="#" class="btn btn-danger btn-xs"><span class=" fa fa-trash "></span></a></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Ultimate Caveman Package </td>
                <td>Yearly</td>
                <td>00-nov-2016</td>
                 <td>$650</td>
                 <td class="text-center"> <a href="#" class="btn btn-danger btn-xs"><span class=" fa fa-trash "></span></a></td>
            </tr>
    </table>
  </div>
  
  </div>



  
  
  	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <hr />
    <h3 > <img src="images/caveman-screening.png" class="caveman-screening-logo" alt=""/> Caveman Screening Results </h3>
    
    
    <div class="row">
      	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    
			<div class="offer offer-danger">
				<div class="shape">
					<div class="shape-text">
						Test 1						
					</div>
				</div>
				<div class="offer-content">
					<p class="test-date">
						Test given on : <label  > 3 - nov - 2016</label>
					</p>
					<p>
						Caveman Screening Scale
						<br> 
                        <div class="progress">
             <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 30%" >
                     30%
                        </div>
                   </div>
					</p>
				</div>
			</div>	</div>
            
              	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="offer offer-primary">
				<div class="shape">
					<div class="shape-text">
						Test 2						
					</div>
				</div>
				<div class="offer-content">
					<p class="test-date">
						Test given on : <label  > 12 - nov - 2016</label>
					</p>
					<p>
						Caveman Screening Scale
						<br> 
                        <div class="progress">
             <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 50%" >
                     50%
                        </div>
                   </div>
					</p>
				</div>
			</div>
            	</div>
              	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="offer offer-success">
				<div class="shape">
					<div class="shape-text">
						Test 3						
					</div>
				</div>
				<div class="offer-content">
					<p class="test-date">
						Test given on : <label  > 15 - nov- 2016</label>
					</p>
					<p>
						Caveman Screening Scale
						<br> 
                        <div class="progress">
             <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%" >
                     80%
                        </div>
                   </div>
					</p>
				</div>
			</div>
            
		</div>	</div>
  
  </div>
  </div>
        </div>
      </div>
    </div></div>
  </section>
</main>


<?php 
get_footer(); 
?>