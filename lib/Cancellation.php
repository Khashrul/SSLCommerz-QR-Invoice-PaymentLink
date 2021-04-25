<?php

namespace Sslcommerz\Invoice;

class Cancellation
{
    private $data = [];
    private $config = [];
    public $cancelInvoiceResponse = [];

    protected $apiUrl;
    protected $storeId;
    protected $storePassword;
    protected $sandbox = false;

    public $error;

    public function __construct($storeid, $storepass, $post_data, $sandbox = false)
    {
        $this->storeId = $storeid;
        $this->storePassword = $storepass;
        $this->sandbox = $sandbox;

        $this->config = include(__DIR__.'/../config/config.php');

        $this->setStoreId($this->storeId);
        $this->setStorePassword($this->storePassword);
        $this->setParamInfo($post_data);
        $this->setEnv($this->sandbox, $this->config['apiUrl']['cancel_payment']);
        $this->getCanceledInvoice($this->config['connect_from_localhost']);
        $this->getResponse();
    }

    protected function setResponse($cancelInvoiceResponse = [])
    {
        $this->cancelInvoiceResponse = $cancelInvoiceResponse;
    }

    protected function getResponse()
    {
        return $this->cancelInvoiceResponse;
    }

    protected function setStoreId($storeID)
    {
        $this->storeId = $storeID;
    }

    protected function getStoreId()
    {
        return $this->storeId;
    }

    protected function setStorePassword($storePassword)
    {
        $this->storePassword = $storePassword;
    }

    protected function getStorePassword()
    {
        return $this->storePassword;
    }

    protected function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
    }

    protected function setEnv($sandBox, $postfix)
    {
        $this->sandbox = $sandBox;

        if($this->sandbox == false) {
            $request_url = "https://sandbox.sslcommerz.com";
        }
        else {
            $request_url = "https://securepay.sslcommerz.com";
        }

        $this->setApiUrl($request_url.$postfix);
    }

    public function setParamInfo(array $info)
    {
        $this->data['store_id'] = $this->getStoreId();
        $this->data['store_passwd'] = $this->getStorePassword();
        $this->data['refer'] = $this->config['refer_id'];
        $this->data['invoice_id'] = (isset($info['invoice_id'])) ? $info['invoice_id'] : '';
        $this->data['action'] = "invoiceCancellation";

        return $this->data;
    }

    public function getCanceledInvoice($setLocalhost = false)
    {
        $curl = curl_init();

        if (!$setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // The default value for this option is 2. It means, it has to have the same name in the certificate as is in the URL you operate against.
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // When the verify value is 0, the connection succeeds regardless of the names in the certificate.
        }

        curl_setopt($curl, CURLOPT_URL, $this->getApiUrl());
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNo = curl_errno($curl);
        curl_close($curl);

        if ($code == 200 & !($curlErrorNo)) {
            $this->setResponse($response);
        } else {
            $this->setResponse(["error" => "FAILED TO CONNECT WITH SSLCOMMERZ API"]);
        }
    }
}