<?php

namespace Sslcommerz\Invoice;

require_once(__DIR__."/InvoiceAbstract.php");

class Invoice extends InvoiceAbstract
{
    private $data = [];
    private $config = [];

    private $storeid;
    private $storepass;
    protected $sandbox = false;
    private $ipnUrl;
    public $generatedInvoice = [];

    /**
     * @var string
     */
    private $error;

    public function __construct($storeid, $storepass, $post_data, $sandbox = false)
    {
        $this->storeid = $storeid;
        $this->storepass = $storepass;
        $this->sandbox = $sandbox;

        $this->config = include(__DIR__.'/../config/config.php');

        $this->setStoreId($this->storeid);
        $this->setStorePassword($this->storepass);
        $this->setEnv($this->sandbox, $this->config['apiUrl']['create_invoice']);
        $this->InitiatePaymentLink($post_data);
        $this->getResponse();
    }

    /**
     * @param array $requestData
     * @param string $type
     * @param string $pattern
     * @return false|mixed|string
     */
    public function InitiatePaymentLink(array $requestData)
    {
        if (empty($requestData)) {
            return "Please provide a valid information list about transaction with transaction id, amount, success url, fail url, cancel url, store id and pass at least";
        }

        $this->setAuthenticationInfo();
        $this->setParams($requestData);
        
        $response = $this->callToApi($this->data, $this->config['connect_from_localhost']);

        $formattedResponse = json_decode($response, true);

        if ($formattedResponse['status'] == 'success') {
            $this->setResponse($formattedResponse);
        } else {
            $this->setResponse($formattedResponse['error_reason']);
        }
    }

    protected function setResponse($generatedInvoice = [])
    {
        $this->generatedInvoice = $generatedInvoice;
    }

    protected function getResponse()
    {
        return $this->generatedInvoice;
    }

    protected function setIpnUrl()
    {
        $this->ipnUrl = $this->config['projectPath'] . '/' . $this->config['ipn_url'];
    }

    protected function getIpnUrl()
    {
        return $this->ipnUrl;
    }

    public function setParams($requestData)
    {
        ## Set IPN URL
        $this->setIpnUrl();

        ##  Integration Required Parameters
        $this->setRequiredInfo($requestData);

        ##  EMI Required Parameters
        $this->setEmiInfo($requestData);

        ##  Customer Information
        $this->setCustomerInfo($requestData);

        ##  Shipment Information
        $this->setShipmentInfo($requestData);

        ##  Product Information
        $this->setProductInfo($requestData);

        ##  Customized or Additional Parameters
        $this->setAdditionalInfo($requestData);
    }

    public function setAuthenticationInfo()
    {
        $this->data['store_id'] = $this->getStoreId();
        $this->data['store_passwd'] = $this->getStorePassword();
        $this->data['refer'] = $this->config['refer_id'];

        return $this->data;
    }

    public function setRequiredInfo(array $info)
    {
        $this->data['amount'] = $info['amount']; 
        $this->data['currency'] = $info['currency'];
        $this->data['tran_id'] = $info['tran_id'];
        $this->data['product_category'] = (isset($info['product_category'])) ? $info['product_category'] : 'Invoice';
        $this->data['acct_no'] = (isset($info['acct_no'])) ? $info['acct_no'] : null;
        $this->data['is_sent_email'] = (isset($info['is_sent_email'])) ? $info['is_sent_email'] : "no";
        $this->data['is_sent_sms'] = (isset($info['is_sent_sms'])) ? $info['is_sent_sms'] : "no";
        $this->data['ipn_url'] = $this->getIpnUrl();

        return $this->data;
    }

    public function setEmiInfo(array $info)
    {
        $this->data['emi_option'] = (isset($info['emi_option'])) ? $info['emi_option'] : 0;
        
        if ($this->data['emi_option'] != 0) {
            $this->data['emi_max_inst_option'] = (isset($info['emi_max_inst_option'])) ? $info['emi_max_inst_option'] : '';
            $this->data['emi_selected_inst'] = (isset($info['emi_selected_inst'])) ? $info['emi_selected_inst'] : '';
            $this->data['emi_allow_only'] = (isset($info['emi_allow_only'])) ? $info['emi_allow_only'] : 0;
        }
        
        return $this->data;
    }

    public function setCustomerInfo(array $info)
    {
        $this->data['cus_name'] = $info['cus_name'];
        $this->data['cus_email'] = $info['cus_email'];
        $this->data['cus_add1'] = (isset($info['cus_add1'])) ? $info['cus_add1'] : null;
        $this->data['cus_add2'] = (isset($info['cus_add2'])) ? $info['cus_add2'] : null;
        $this->data['cus_city'] = (isset($info['cus_city'])) ? $info['cus_city'] : null;
        $this->data['cus_state'] = (isset($info['cus_state'])) ? $info['cus_state'] : null;
        $this->data['cus_postcode'] = (isset($info['cus_postcode'])) ? $info['cus_postcode'] : null;
        $this->data['cus_country'] = (isset($info['cus_country'])) ? $info['cus_country'] : 'Bangladesh';
        $this->data['cus_phone'] = $info['cus_phone'];
        $this->data['cus_fax'] = (isset($info['cus_fax'])) ? $info['cus_fax'] : null;

        return $this->data;
    }

    public function setShipmentInfo(array $info)
    {

        $this->data['shipping_method'] = (isset($info['shipping_method'])) ? $info['shipping_method'] : "NO";
        $this->data['num_of_item'] = (isset($info['num_of_item'])) ? $info['num_of_item'] : "1";
        
        if($this->data['shipping_method'] != "NO") {
            $this->data['ship_name'] = (isset($info['ship_name'])) ? $info['ship_name'] : null;
            $this->data['ship_add1'] = (isset($info['ship_add1'])) ? $info['ship_add1'] : null;
            $this->data['ship_add2'] = (isset($info['ship_add2'])) ? $info['ship_add2'] : null;
            $this->data['ship_city'] = (isset($info['ship_city'])) ? $info['ship_city'] : null;
            $this->data['ship_state'] = (isset($info['ship_state'])) ? $info['ship_state'] : null; 
            $this->data['ship_postcode'] = (isset($info['ship_postcode'])) ? $info['ship_postcode'] : null; 
            $this->data['ship_country'] = (isset($info['ship_country'])) ? $info['ship_country'] : null;
        }

        return $this->data;
    }

    public function setProductInfo(array $info)
    {

        $this->data['product_name'] = (isset($info['product_name'])) ? $info['product_name'] : 'Invoice';
        $this->data['product_category'] = (isset($info['product_category'])) ? $info['product_category'] : 'Invoice'; 
        /*
         * String (100)
         * Mandatory - Mention goods vertical. It is very much necessary for online transactions to avoid chargeback.
         * Please use the below keys :
            1) general
            2) physical-goods
            3) non-physical-goods
            4) airline-tickets
            5) travel-vertical
            6) telecom-vertical
        */
        $this->data['product_profile'] = (isset($info['product_profile'])) ? $info['product_profile'] : 'general';

        $this->data['hours_till_departure'] = (isset($info['hours_till_departure'])) ? $info['hours_till_departure'] : null;
        $this->data['flight_type'] = (isset($info['flight_type'])) ? $info['flight_type'] : null;
        $this->data['pnr'] = (isset($info['pnr'])) ? $info['pnr'] : null; 
        $this->data['journey_from_to'] = (isset($info['journey_from_to'])) ? $info['journey_from_to'] : null;
        $this->data['third_party_booking'] = (isset($info['third_party_booking'])) ? $info['third_party_booking'] : null;
        $this->data['hotel_name'] = (isset($info['hotel_name'])) ? $info['hotel_name'] : null;
        $this->data['length_of_stay'] = (isset($info['length_of_stay'])) ? $info['length_of_stay'] : null;
        $this->data['check_in_time'] = (isset($info['check_in_time'])) ? $info['check_in_time'] : null;
        $this->data['hotel_city'] = (isset($info['hotel_city'])) ? $info['hotel_city'] : null;
        $this->data['product_type'] = (isset($info['product_type'])) ? $info['product_type'] : null;
        $this->data['topup_number'] = (isset($info['topup_number'])) ? $info['topup_number'] : null;
        $this->data['country_topup'] = (isset($info['country_topup'])) ? $info['country_topup'] : null;

        /*
         * Type: JSON
         * JSON data with two elements. product : Max 255 characters, quantity : Quantity in numeric value and amount : Decimal (12,2)
         * Example:
           [{"product":"DHK TO BRS AC A1","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A2","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A3","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A4","quantity":"2","amount":"200.00"}]
         * */
        $this->data['cart'] = (isset($info['cart'])) ? $info['cart'] : null;
        $this->data['product_amount'] = (isset($info['product_amount'])) ? $info['product_amount'] : null;
        $this->data['vat'] = (isset($info['vat'])) ? $info['vat'] : null;
        $this->data['discount_amount'] = (isset($info['discount_amount'])) ? $info['discount_amount'] : null;
        $this->data['convenience_fee'] = (isset($info['convenience_fee'])) ? $info['convenience_fee'] : null;

        return $this->data;
    }

    public function setAdditionalInfo(array $info)
    {
        $this->data['value_a'] = (isset($info['value_a'])) ? $info['value_a'] : null;
        $this->data['value_b'] = (isset($info['value_b'])) ? $info['value_b'] : null;
        $this->data['value_c'] = (isset($info['value_c'])) ? $info['value_c'] : null;
        $this->data['value_d'] = (isset($info['value_d'])) ? $info['value_d'] : null;

        return $this->data;
    }
}