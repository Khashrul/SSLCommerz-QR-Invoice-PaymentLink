<?php 
require_once("../lib/Validator.php");
require_once("config/config.php");

use Sslcommerz\Invoice\Validator;

$valid = new Validator(STOREID, STOREPASS, $_POST, ENV);


?>