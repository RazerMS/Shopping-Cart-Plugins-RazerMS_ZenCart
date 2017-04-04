<?php
/**
 * MOLPay ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author MOLPay Technical Team <technical@molpay.com>
 * @version 2.0.0
 */

ob_start();

    define('MODULE_PAYMENT_MOLPAY_TEXT_CATALOG_TITLE', 'Secure Credit Card Payment');
    define('MODULE_PAYMENT_MOLPAY_TEXT_ADMIN_TITLE', 'MOLPay Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    define('MODULE_PAYMENT_MOLPAY_TEXT_DESCRIPTION', '<b>MOLPay ZenCart Module</b> is a service to help Malaysian merchant to sell online.<br><br> Visit <a href=http://www.molpay.com target=_blank>http://www.molpay.com</a> for more info regarding to MOLPay Online Payment Gateway');
    define('MODULE_PAYMENT_MOLPAY_TEXT_ERROR_MESSAGE', 'Your transaction has been cancelled or declined.  Please try again');
    //define('MODULE_PAYMENT_MOLPAY_MARK_BUTTON_IMG', '<img src=https://www.onlinepayment.com.my/NBepay/images/logo_nbepay.gif border=0 alt=MOLPay Online Payment Gateway title=MOLPay Online Payment Gateway>');
    define('MODULE_PAYMENT_MOLPAY_TEXT_TITLE','MOLPay Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    
    define('MODULE_PAYMENT_MOLPAY_MARK_BUTTON_IMG', 'http://www.molpay.com/v3/images/content/molpay-logo/molpay-logo-100x50.jpg');
    define('MODULE_PAYMENT_MOLPAY_ACCEPTANCE_MARK_TEXT', 'MOLPay Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    define('MODULE_PAYMENT_MOLPAY_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_MOLPAY_MARK_BUTTON_IMG . '" width="57" height="25"
     <span class="smallText">' . MODULE_PAYMENT_MOLPAY_ACCEPTANCE_MARK_TEXT . '</span>');

ob_end_clean();

?>
