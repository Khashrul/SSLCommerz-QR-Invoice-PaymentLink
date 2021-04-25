<?php 

require_once("../../lib/invoice.php");
require_once("../config/config.php");

use Sslcommerz\Invoice\Invoice;

$tranid = strtoupper(uniqid());

$post_data = array();
$post_data['tran_id'] = $tranid;
$post_data['acct'] = "SIQ".$tranid;
$post_data['cus_name'] = (isset($_POST['name'])) ? $_POST['name'] : '';
$post_data['cus_email'] = (isset($_POST['email'])) ? $_POST['email'] : '';
$post_data['cus_add1'] = "New Eskaton";
$post_data['cus_add2'] = "93 B";
$post_data['cus_city'] = "Dhaka";
$post_data['cus_state'] = "Dhaka";
$post_data['cus_postcode'] = "1000";
$post_data['cus_country'] = "Bangladesh";
$post_data['cus_phone'] = (isset($_POST['phone'])) ? $_POST['phone'] : '';
$post_data['ship_name'] = (isset($_POST['name'])) ? $_POST['name'] : '';
$post_data['ship_add1'] = "New Eskaton";
$post_data['ship_add2'] = "93 B";
$post_data['ship_city'] = "Dhaka";
$post_data['ship_state'] = "Dhaka";
$post_data['ship_postcode'] = "1000";
$post_data['ship_country'] = "Bangladesh";
$post_data['value_a'] = "SSL";
$post_data['value_b'] = "12345";
$post_data['amount'] = (isset($_POST['amount'])) ? $_POST['amount'] : '';
$post_data['currency'] = "BDT";
$post_data['shipping_method'] = 'NO';
$post_data['num_of_item'] = '2';
$post_data['product_name'] = 'T-Shirt, Pant';
$post_data['product_profile'] = 'physical-goods';
$post_data['product_category'] = 'clothing';

$cart[0]['sl'] = 1;
$cart[0]['product'] = "T-Shirt";
$cart[0]['quantity'] = "1";
$cart[0]['unitprice'] = "1000";
$cart[0]['amount'] = "1000";
$cart[1]['sl'] = 2;
$cart[1]['product'] = "Pant";
$cart[1]['quantity'] = "1";
$cart[1]['unitprice'] = "1000";
$cart[1]['amount'] = "1000";
$post_data['cart'] = json_encode($cart);
$post_data['is_sent_email'] = "yes";

$invoice = new Invoice(STOREID, STOREPASS, $post_data, ENV);

echo json_encode($invoice->generatedInvoice, true);
