<?php 

require_once("../config/config.php");
require_once("../../lib/Status.php");

use Sslcommerz\Invoice\Status;

$post_data = array();
$post_data['invoice_id'] = (isset($_POST['invid'])) ? $_POST['invid'] : '';

$invoicestatus = new Status(STOREID, STOREPASS, $post_data, ENV);

echo json_encode(json_decode($invoicestatus->statusResponse, true), JSON_UNESCAPED_SLASHES);
