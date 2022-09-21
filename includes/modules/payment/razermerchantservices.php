<?php
/**
 * Razer Merchant Services ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author Razer Merchant Services Technical Team <technical-sa@razer.com>
 * @version 2.0.0
 */
 
class razermerchantservices {
    public  $code,
            $title, 
            $description, 
            $enabled;

    function razermerchantservices ()  {
        global $db, $order;
        $this->code = 'razermerchantservices';
        $this->title = MODULE_PAYMENT_RMS_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_RMS_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_RMS_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_RMS_STATUS == 'True') ? true : false);
        $this->form_action_url = ((MODULE_PAYMENT_RMS_TYPE == 'live') ? "https://www.onlinepayment.com.my/MOLPay/pay/".MODULE_PAYMENT_RMS_ID."/" : "https://sandbox.merchant.razer.com/MOLPay/pay/".MODULE_PAYMENT_RMS_ID."/");

        if ((int)MODULE_PAYMENT_RMS_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_RMS_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();

    }

    function update_status() {
        global $order, $db;
        if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_RMS_ZONE > 0) ) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_RMS_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while (!$check->EOF) {
                if ($check->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                }
                elseif ($check->fields['zone_id'] == $order->billing['zone_id'])  {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     * Check the user input submited on checkout_payment.php with javascript (client-side).
     * 
     * @return boolean
     */
    function javascript_validation() {
        return false;
    }
    
    /**
     * Display on payment selection
     * 
     * @global type $order
     * @return array
     */
    function selection() {
        global $order;
        
         return array('id' => $this->code,
                 'module' => MODULE_PAYMENT_RMS_TEXT_CATALOG_LOGO,
                 'icon' => MODULE_PAYMENT_RMS_TEXT_CATALOG_LOGO
                 );

    }

    function pre_confirmation_check() {
        global $_POST;
        return false;
    }

    function confirmation() 
    {

       global $_POST, $languages_id, $shipping_cost, $total_cost, $shipping_selected, $shipping_method, $currencies, $currency, $customer_id , $db, $order;

        $customer_id = $_SESSION['customer_id'];
        $customer_query = "SELECT c.`customers_firstname` , c.`customers_lastname` , c.`customers_email_address` , c.`customers_telephone`, ab.`entry_company` ,  ab.`entry_street_address` ,  ab.`entry_suburb` , ab.`entry_postcode` , ab.`entry_city` , ab.`entry_state` , ab.`entry_country_id` , 
              ab.`entry_zone_id` FROM ".TABLE_CUSTOMERS." c JOIN ".TABLE_ADDRESS_BOOK." ab ON ( c.`customers_default_address_id` = ab.`address_book_id` ) 
              WHERE c.`customers_id` = ".$customer_id;
        $customer_info = $db->Execute($customer_query);
        $customer_info->fields['format_id'] = zen_get_address_format_id($customer_info->fields['entry_country_id']);
        $customer_info->fields['country_name'] = zen_get_country_name($customer_info->fields['entry_country_id']);
        
        $curr_obj = $order->info;

        $OrderAmt = number_format($order->info['total'] * $order->info['currency_value'], $currencies->get_decimal_places($order->info['currency']), '.', '') ; 

        $order_query = array('customers_id' => $customer_id,
               'customers_name' => $customer_info->fields['customers_firstname'] . " " . $customer_info->fields['customers_lastname'],
               'customers_company' => $customer_info->fields['entry_company'],
               'customers_street_address' => $customer_info->fields['entry_street_address'],
               'customers_suburb' => $customer_info->fields['entry_suburb'],
               'customers_city' => $customer_info->fields['entry_city'],
               'customers_postcode' => $customer_info->fields['entry_postcode'],
               'customers_state' => $customer_info->fields['entry_state'],
               'customers_country' => $customer_info->fields['country_name'],
               'customers_telephone' => $customer_info->fields['customers_telephone'],
               'customers_email_address' => $customer_info->fields['customers_email_address'],
               'customers_address_format_id' => $customer_info->fields['format_id'],
               'delivery_name' => $customer_info->fields['customers_firstname'] . " " . $customer_info->fields['customers_lastname'],
               'delivery_company' => $customer_info->fields['entry_company'],
               'delivery_street_address' => $customer_info->fields['entry_street_address'],
               'delivery_suburb' => $customer_info->fields['entry_suburb'],
               'delivery_city' => $customer_info->fields['entry_city'],
               'delivery_postcode' => $customer_info->fields['entry_postcode'],
               'delivery_state' => $customer_info->fields['entry_state'],
               'delivery_country' => $customer_info->fields['country_name'],
               'delivery_address_format_id' => $customer_info->fields['format_id'],
               'billing_name' => $customer_info->fields['customers_firstname'] . " " . $customer_info->fields['customers_lastname'],
               'billing_company' => $customer_info->fields['entry_company'],
               'billing_street_address' => $customer_info->fields['entry_street_address'],
               'billing_suburb' => $customer_info->fields['entry_suburb'],
               'billing_city' => $customer_info->fields['entry_city'],
               'billing_postcode' => $customer_info->fields['entry_postcode'],
               'billing_state' => $customer_info->fields['entry_state'],
               'billing_country' => $customer_info->fields['country_name'],
               'billing_address_format_id' => $customer_info->fields['format_id'],
               'shipping_method' => $order->info['shipping_method'],
               'shipping_module_code' => $order->info['shipping_module_code'],
               'payment_method' => 'RMS Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)',
               'payment_module_code' => 'rms',
               'coupon_code' => $order->info['coupon_code'],
               'date_purchased' => 'now()', 
               'orders_status' => DEFAULT_ORDERS_STATUS_ID,
               'currency' => $order->info['currency'],
               'currency_value' =>  $order->info['currency_value'],
               'order_total' => $OrderAmt,
               'order_tax' => '0.00',
               'paypal_ipn_id' => '0',
               'ip_address' => $_SERVER['REMOTE_ADDR']. " - " . $_SERVER['REMOTE_ADDR']
               );

        zen_db_perform(TABLE_ORDERS, $order_query);
        $insert_id = $db->insert_ID();
        
        //Insert Order status History
        $order_status = array('orders_id' => $insert_id,
                          'orders_status_id' => DEFAULT_ORDERS_STATUS_ID,
                          'date_added' => 'now()'
                         );

        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $order_status);

        //Insert Order Total
        $order_total = array('orders_id' => $insert_id,
                            'title' => 'Sub-Total',
                            'text' => $order->info['currency']." ".$order->info['subtotal'],
                            'value' => $order->info['subtotal'], 
                            'class' => "ot_subtotal", 
                            'sort_order' => "1");

        zen_db_perform(TABLE_ORDERS_TOTAL, $order_total);

        $order_total = array('orders_id' => $insert_id,
                            'title' => $order->info['shipping_method'],
                            'text' => $order->info['currency']." ".$order->info['shipping_cost'],
                            'value' => $order->info['shipping_cost'], 
                            'class' => "ot_shipping", 
                            'sort_order' => "2");

        zen_db_perform(TABLE_ORDERS_TOTAL, $order_total);

        $order_total = array('orders_id' => $insert_id,
                            'title' => 'Total',
                            'text' => $order->info['currency']." ".$OrderAmt,
                            'value' => $OrderAmt, 
                            'class' => "ot_total", 
                            'sort_order' => "3");

        zen_db_perform(TABLE_ORDERS_TOTAL, $order_total);

        $customers_query = $db -> Execute("select customers_id, CONCAT( customers_firstname, ' ', customers_lastname ) AS customers_fullname, customers_email_address from " . TABLE_CUSTOMERS . " ORDER BY customers_firstname");
        while(!$customers_query -> EOF) 
        {
            $customers[] = array('id' => $customers_query->fields['customers_id'],
                               'text' => $customers_query->fields['customers_fullname'].' ('.$customers_query->fields['customers_email_address'].')');
            $customers_query->MoveNext();
        }
	
        foreach($order->products as $product)
        {
		$sql_data_array = array(
					'orders_id'=> $insert_id,
					'products_id'=> strtok($product['id'], ':'),
					'products_model'=> $product['model'],
					'products_name'=> $product['name'],
					'products_price'=> $product['price'],
					'final_price'=> $product['final_price'],
					'products_tax'=> $product['tax'],
					'products_quantity'=> $product['qty'],
					'onetime_charges'=> $product['onetime_charges'],
					'products_priced_by_attribute'=> $product['products_priced_by_attribute'],
					'product_is_free'=> $product['product_is_free'],
					'products_discount_type'=> $product['products_discount_type'],
					'products_discount_type_from'=> $product['products_discount_type_from'],
					'products_prid' => $product['id'],
                    'products_weight' => $product['products_weight'],
                    'products_virtual' => $product['products_virtual'],
                    'product_is_always_free_shipping' => $product['product_is_always_free_shipping'],
                    'products_quantity_order_min' => $product['products_quantity_order_min'],
                    'products_quantity_order_units' => $product['products_quantity_order_units'],
                    'products_quantity_order_max' => $product['products_quantity_order_max'],
                    'products_quantity_mixed' =>  $product['products_quantity_mixed'],
                    'products_mixed_discount_quantity' => $product['products_mixed_discount_quantity']);

		zen_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
           
		$db->Execute("update " . TABLE_PRODUCTS . "
                                set products_quantity = products_quantity - '". $product['qty'] ."'
                                where products_id = '" . strtok($product['id'], ':'). "'");
        }
       
        return false;
    }

    function process_button() {
        global $_POST, $languages_id, $shipping_cost, $total_cost, $shipping_selected, $shipping_method, $currencies, $currency, $customer_id , $db, $order;

        $prod = $order->product;

        while ( list($key,$val) = each($order->products) ) {
            $pname.= $val[name]." x ".$val[qty]."\n";  
        }

        $zenId = zen_session_name() . '=' . zen_session_id();
        $cartId = zen_session_id();
        $curr_obj = $order->info;
        $currency = $curr_obj[currency];

        $OrderAmt = number_format($order->info['total'] * $currencies->get_value($currency), $currencies->get_decimal_places($currency), '.', '') ; 

        $oid_sql = "select Max(orders_id) as oid from ".TABLE_ORDERS." ";
        $oid = $db->Execute($oid_sql);
        $oid = $oid->fields['oid'];
        $returnurl = MODULE_PAYMENT_RMS_RETURNURL;
        $vcode = md5($OrderAmt.MODULE_PAYMENT_RMS_ID.$oid.MODULE_PAYMENT_RMS_VKEY);

        $process_button_string = 
        zen_draw_hidden_field('currency', strtoupper($currency)) . 
        zen_draw_hidden_field('bill_desc', $pname) .
        zen_draw_hidden_field('orderid', $oid) .
        zen_draw_hidden_field('returnurl', $returnurl).
        zen_draw_hidden_field('vcode', $vcode).
        zen_draw_hidden_field('amount', $OrderAmt).
        zen_draw_hidden_field('bill_desc', 'Payment to '.STORE_NAME) ;

        $language_code_raw = "select code from " . TABLE_LANGUAGES . " where languages_id ='$languages_id'";
        $language_code = $db->Execute($language_code_raw);

        $process_button_string.=	
        zen_draw_hidden_field('bill_name', $order->customer['firstname'] . ' ' . $order->customer['lastname']) .
        zen_draw_hidden_field('country', $order->customer['country']['iso_code_2']) .
        zen_draw_hidden_field('bill_mobile', $order->customer['telephone']) .
        zen_draw_hidden_field('bill_email', $order->customer['email_address']) ;

        return $process_button_string;
    }

    function before_process() {
        //global $_POST;
    }

    function after_process() {
        return false;
    }

    function get_error() {
        global $_GET;

        $error = array('title'=>'RMS Error',
                       'error'=>'Error Detail');
    }

    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_RMS_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    function install() {
        global $db;
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values 
        ('Enable RMS Module', 'MODULE_PAYMENT_RMS_STATUS', 'True', 'Do you want to accept RMS payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('RMS Merchant ID', 'MODULE_PAYMENT_RMS_ID', '', 'Your RMS Merchant ID', '6', '2', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('RMS secret key', 'MODULE_PAYMENT_RMS_SECRETKEY', '', 'Please refer your RMS merchant profile to have this key', '6', '5', now())");
         
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('RMS verify key', 'MODULE_PAYMENT_RMS_VKEY', '', 'Please refer your RMS merchant profile to have this key', '6', '5', now())"); 
		 
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values 
         ('Live or Sandbox', 'MODULE_PAYMENT_RMS_TYPE', 'live', '<strong>Live: </strong> Used to process Live transactions<br><strong>Sandbox: </strong>For developers and testing', '6', '25', 'zen_cfg_select_option(array(\'live\', \'sandbox\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('RMS multiple return url', 'MODULE_PAYMENT_RMS_RETURNURL', '', 'Provide RMS Multi Return URL if you wish to have this fetaures. <i>e.g : http://www.yourdomain.com/process.php</i>', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('Sort order of display.', 'MODULE_PAYMENT_RMS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values
         ('Set Order Status', 'MODULE_PAYMENT_RMS_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

    }

    function remove() {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

////////////////////////////////////////////////////
// Create our Key - > Value Arrays
////////////////////////////////////////////////////
    function keys() {
        return array(
            'MODULE_PAYMENT_RMS_STATUS'
            ,'MODULE_PAYMENT_RMS_ID'
            ,'MODULE_PAYMENT_RMS_VKEY'
	        ,'MODULE_PAYMENT_RMS_SECRETKEY'
	        ,'MODULE_PAYMENT_RMS_TYPE'
            ,'MODULE_PAYMENT_RMS_RETURNURL'
            ,'MODULE_PAYMENT_RMS_SORT_ORDER'
            ,'MODULE_PAYMENT_RMS_ORDER_STATUS_ID'
        );
    }
}
?>