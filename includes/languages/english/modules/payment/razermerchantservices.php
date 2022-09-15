<?php
/**
 * Razer Merchant Services (RMS) ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author Razer Merchant Services (RMS) Technical Team <technical-sa@razer.com>
 * @version 2.0.0
 */

ob_start();

    define('MODULE_PAYMENT_RMS_TEXT_CATALOG_TITLE', 'Secure Credit Card Payment');
    define('MODULE_PAYMENT_RMS_TEXT_ADMIN_TITLE', 'Razer Merchant Services Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    define('MODULE_PAYMENT_RMS_TEXT_DESCRIPTION', '<b>Razer Merchant Services ZenCart Module</b> is a service to help Malaysian merchant to sell online.<br><br> Visit <a href=http://www.merchant.razer.com target=_blank>http://www.merchant.razer.com</a> for more info regarding to Razer Merchant Services Online Payment Gateway');
    define('MODULE_PAYMENT_RMS_TEXT_ERROR_MESSAGE', 'Your transaction has been cancelled or declined.  Please try again');
    define('MODULE_PAYMENT_RMS_TEXT_TITLE','Razer Merchant Services Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    define('MODULE_PAYMENT_RMS_MARK_BUTTON_IMG', 'https://d2x73ruoixi2ei.cloudfront.net/images/rms-logo-400x160.png');
    define('MODULE_PAYMENT_RMS_ACCEPTANCE_MARK_TEXT', 'Razer Merchant Services Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)');
    define('MODULE_PAYMENT_RMS_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_RMS_MARK_BUTTON_IMG . '" width="57" height="25"
     <span class="smallText">' . MODULE_PAYMENT_RMS_ACCEPTANCE_MARK_TEXT . '</span>');

ob_end_clean();

?>
