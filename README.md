### SSLCommerz-QR/Invoice/Payment Link

This library will help you to integrate the QR,Invoice and Payment link with your system.

### Library Directory

```
 |-- client/ (Example File)
    |-- config/config.php
    |-- include/
        |-- footer.php
        |-- header.php
    |-- remote/
        |-- cancel.php (Back end)
        |-- request.php (Back end)
        |-- status.php (Back end)
    |-- cancel_invoice.php (Front end)
    |-- check_invoice_status.php (Front end)
    |-- create_invoice.php (Front end)
    |-- ipn_validation.php (IPN Script)

 |-- config/config.php (Core Config)
 |-- lib/
    |-- InvoiceInterface.php (Core File)
    |-- InvoiceAbstract.php (Core File)
    |-- Invoice.php (Core File)
    |-- Cancellation.php (Core File)
    |-- Status.php (Core File)
    |-- Validator.php (Core File)
 |-- README.md
```


### Installation of Core Library With Your Project:

* __Step 1:__ Download and extract the library files into your project (`config & lib folder`).
* __Step 2:__ Go to `config.php` script and set your `PROJECT_PATH, REFER_ID(From SSLC Merchant Panel) and ipn_url`.
* __Step 3:__ Now include the config and library script with your controller script.
```
require_once("../config/config.php");
require_once("../lib/invoice.php");
require_once("../lib/Cancellation.php");
require_once("../lib/Status.php");
require_once("../lib/Validator.php");

use Sslcommerz\Invoice\Invoice;
use Sslcommerz\Invoice\Cancellation;
use Sslcommerz\Invoice\Status;
use Sslcommerz\Invoice\Validator;
```
* __Step 4:__ To initiate the invoice, status, calcel & IPN validation request call below objects.
`ENV` = `false`(default sandbox)/`true` (live)

```
new Invoice(YOUR STOREID, YOUR STOREPASS, $post_data, ENV); // To create invoice
new Cancellation(YOUR STOREID, YOUR STOREPASS, $post_data, ENV); // To cancel invoice
new Status(YOUR STOREID, YOUR STOREPASS, $post_data, ENV); // To check the invoice status
new Validator(YOUR STOREID, YOUR STOREPASS, $_POST, ENV); // To validate the transaction by IPN
```
* __Step 5:__ To initiate the invoice use below sample parameter.
```
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
```
* __Step 6:__ To cancel & check the invoice status use below sample parameter.
```
$post_data = array();
$post_data['invoice_id'] = (isset($_POST['invid'])) ? $_POST['invid'] : '';
```


### Installation of Example Project:
* __Step 1:__ Download and extract the library & client(Example) files (`config, lib, client folder`).
* __Step 2:__ Go to `client/config.php` script and set your `STOREID,STOREPASS,ENV`.
`ENV` = `false`(default sandbox)/`true` (live)
* __Step 3:__ Go to core `config.php` script and set your `PROJECT_PATH, REFER_ID(From SSLC Merchant Panel) and ipn_url`.

* Now the project is ready to use.


### Image Reference:
![Example](https://github.com/prabalsslw/images/blob/master/inv1.png)
![Example](https://github.com/prabalsslw/images/blob/master/inv2.png)
![Example](https://github.com/prabalsslw/images/blob/master/inv3.png)
![Example](https://github.com/prabalsslw/images/blob/master/inv4.png)

#### For any code related confusion please check the example code.

### Contributors

> Prabal Mallick
> 
> Email: integration@sslcommerz.com

