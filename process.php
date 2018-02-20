<?php

/**
 * MOLPay ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author MOLPay Technical Team <technical@molpay.com>
 * @version 2.0.0
 */

require('includes/application_top.php');
global $db, $order;

$info = ( $HTTP_POST_VARS )?$HTTP_POST_VARS:$_POST;

$domain	  = $info['domain'];
$amount   = $info['amount'];
$orderid  = $info['orderid'];
$tranID   = $info['tranID'];
$appcode  = $info['appcode'];
$status	  = $info['status'];
$currency = $info['currency'];
$paydate  = $info['paydate'];
$channel  = $info['channel'];
$skey     = $info['skey'];
$password = MODULE_PAYMENT_MOLPAY_VKEY;

$excluded = array ("domain", "skey", "nbcb", "treq");
$comment = "Payment Info\n\r";

foreach($info as $k => $v){
    if(!in_array($k, $excluded))
        $comment .= $k." :".$v.", ";
}

if($status == "11")
  $sr = "Failed";
elseif($status == "22")
  $sr = "Pending";
elseif($status == "00")
  $sr = "Success";

$comment .= "Status Remark :".$sr;

$key0 = md5($tranID.$orderid.$status.$domain.$amount.$currency);
$key1 = md5($paydate.$domain.$key0.$appcode.$password);

$ssl = "NONSSL";
if ( ENABLE_SSL != "false" ) 
{
	$ssl = "SSL";
}

/***********************************************************
* Snippet code in purple​color is the enhancement required
* by merchant to add into their return script in order to
* implement backend acknowledge method for IPN
************************************************************/

$_POST['treq']	= 1;// Additional parameter for IPN

while ( list($k,$v) = each($_POST) ) 
{
	$postData[]= $k."=".$v;
}
$postdata 	=implode("&",$postData);
$url 		=((MODULE_PAYMENT_MOLPAY_TYPE == 'live') ? "https://www.onlinepayment.com.my/MOLPay/API/chkstat/returnipn.php" : "https://sandbox.molpay.com/MOLPay/API/chkstat/returnipn.php");
$ch 		=curl_init();
curl_setopt($ch, CURLOPT_POST , 1 );
curl_setopt($ch, CURLOPT_POSTFIELDS , $postdata );
curl_setopt($ch, CURLOPT_URL , $url );
curl_setopt($ch, CURLOPT_HEADER , 1 );
curl_setopt($ch, CURLINFO_HEADER_OUT , TRUE );
curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , FALSE);
$result = curl_exec( $ch );
curl_close( $ch );

if($skey != $key1)
{
        $nb_error = "Payment failed. Please make payment again. ";
        $messageStack->add_session('checkout_payment', $nb_error, 'error');

        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', $ssl, true, false));
        exit();
}

if ($status=="00") 
{
	$db->Execute("update " . TABLE_ORDERS . "
			      set orders_status = 2
	                      where orders_id = '" . (int)$orderid . "'");
	                        
        $sql_data_array = array('orders_id' => (int)$orderid,
                            		'orders_status_id' => 2,
		                        'date_added' => 'now()',
                            		'customer_notified' => false,
                            		'comments'=>$comment
        );

        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

	$db->Execute("delete from ". TABLE_CUSTOMERS_BASKET);
	unset($_SESSION['cart']);
		
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', $ssl, true, false));
}
elseif($status=="22")
{
        $db->Execute("update " . TABLE_ORDERS_STATUS_HISTORY . " set comments = '". $comment ."' where orders_id = '" . (int)$orderid . "'");
								
	$db->Execute("delete from ". TABLE_CUSTOMERS_BASKET);
	unset($_SESSION['cart']);
	
	$nb_error = "Cash payment request successfully created. Please make payment within the given period else your order will not be process.";                
	$messageStack->add_session('checkout_cash', $nb_error, 'success');
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', $ssl, true, false));
}
elseif($status=="11")
{
        $db->Execute("update " . TABLE_ORDERS_STATUS_HISTORY . " set comments = '". $comment ."' where orders_id = '" . (int)$orderid . "'");
                                      
	$nb_error = "Payment failed. Please make payment again. ";
	$messageStack->add_session('checkout_payment', $nb_error, 'error');
		
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', $ssl, true, false));
}


exit();
?>

