<?php
require_once('dbConfig.php');

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

