<?php
/**
 * Razer Merchant Services (RMS) ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author Razer Merchant Services Technical Team <technical-sa@razer.com>
 * @version 2.0.0
 */

require('includes/application_top.php');

$info = ( $HTTP_POST_VARS )?$HTTP_POST_VARS:$_POST;

$nbcb 	  = $_POST['nbcb'];

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
$password = MODULE_PAYMENT_RMS_VKEY;

$excluded = array ("domain", "skey", "nbcb", "treq", "extraP");
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

if($skey != $key1)
{
     exit();    
}
if ($status=="00")
{
  $db->Execute("update " . TABLE_ORDERS . " set orders_status = 2 where orders_id = '" . (int)$orderid . "'");

    $sql_data_array = array('orders_id' => (int)$orderid,
                            'orders_status_id' => 2,
                            'date_added' => 'now()',
                            'customer_notified' => 0,
                            'comments' => $comment
                           );

    zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

}
elseif($status=="11")
{
  $db->Execute("update " . TABLE_ORDERS . " set orders_status = 1 where orders_id = '" . (int)$orderid . "'");
    
                $db->Execute("update " . TABLE_ORDERS_STATUS_HISTORY . "
                              set comments ='" .$comment."' where orders_id = '" . (int)$orderid . "'");
}
elseif($status=="22")
{
  $db->Execute("update " . TABLE_ORDERS . " set orders_status = 1 where orders_id = '" . (int)$orderid . "'");
    
  $db->Execute("update " . TABLE_ORDERS_STATUS_HISTORY . " set comments ='" .$comment."' where orders_id = '" . (int)$orderid . "'");
}

if($nbcb==1)
{
        echo 'CBTOKEN:MPSTATOK';
        exit();
}

?>
