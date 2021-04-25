<?php 

require_once("../config/config.php");
require_once("../../lib/Cancellation.php");

use Sslcommerz\Invoice\Cancellation;

$post_data = array();
$post_data['invoice_id'] = (isset($_POST['invid'])) ? $_POST['invid'] : '';

$canceldata = new Cancellation(STOREID, STOREPASS, $post_data, ENV);

// print_r($canceldata);exit;

echo json_encode(json_decode($canceldata->cancelInvoiceResponse, true), JSON_UNESCAPED_SLASHES);
