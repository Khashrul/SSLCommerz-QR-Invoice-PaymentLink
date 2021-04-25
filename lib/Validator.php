<?php

namespace Sslcommerz\Invoice;

class Validator
{
    private $data = [];
    private $config = [];
    public $validationResponse = [];

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
        $this->setEnv($this->sandbox, $this->config['apiUrl']['payment_validation']);
        $this->validate($post_data);
        $this->getResponse();
    }

    protected function setResponse($validationResponse = [])
    {
        $this->validationResponse = $validationResponse;
    }

    protected function getResponse()
    {
        return $this->validationResponse;
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

    protected function callToValidation($trans_id, $trans_amount, $trans_currency, $post_data, $setLocalhost = false)
    {
        if ($trans_id != "" && $trans_amount != 0) 
        {
            $val_id         = urlencode($post_data['val_id']);
            $store_id       = urlencode($this->getStoreId());
            $store_passwd   = urlencode($this->getStorePassword());

            $requested_url  = ($this->getApiUrl() . "?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");

            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $requested_url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

            if ($setLocalhost) {
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            } else {
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, true);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
            }

            $result = curl_exec($handle);

            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if ($code == 200 && !(curl_errno($handle))) {
                # TO CONVERT AS OBJECT
                $result = json_decode($result);

                # TRANSACTION INFO
                $status = $result->status;
                $tran_id = $result->tran_id;
                $amount = $result->amount;
                $currency_type = $result->currency_type;
                $currency_amount = $result->currency_amount;

                # GIVE SERVICE
                if ($status == "VALID" || $status == "VALIDATED") {
                    if ($trans_currency == "BDT") {
                        if (trim($trans_id) == trim($tran_id) && (abs($trans_amount - $amount) < 1) && trim($trans_currency) == trim('BDT')) {
                            $this->setResponse([true, $result]);
                        } else {
                            # DATA TEMPERED
                            echo "Data has been tempered1";
                            return false;
                        }
                    } 
                    else {
                        if (trim($trans_id) == trim($tran_id) && (abs($trans_amount - $currency_amount) < 1) && trim($trans_currency) == trim($currency_type)) {
                            $this->setResponse([true, $result]);
                        } else {
                            # DATA TEMPERED
                            echo "Data has been tempered2";
                            return false;
                        }
                    }
                } 
                else {
                    # FAILED TRANSACTION
                    echo "Failed Transaction";
                    return false;
                }
            } 
            else {
                # Failed to connect with SSLCOMMERZ
                echo "Faile to connect with SSLCOMMERZ";
                return false;
            }
        } 
        else {
            # INVALID DATA
            echo "Invalid data";
            return false;
        }
    }

    public function validate($post_data)
    {
        if ($post_data == '' && !is_array($post_data)) {
            $this->error = "Please provide valid transaction ID and post request data";
            return $this->error;
        }
        else {
            $trx_id = (!empty($post_data['tran_id'])) ? $post_data['tran_id'] : '';
            $amount = (!empty($post_data['currency_amount'])) ? $post_data['currency_amount'] : 0;
            $currency = (!empty($post_data['currency_type'])) ? $post_data['currency_type'] : 'BDT';

            $validation = $this->callToValidation($trx_id, $amount, $currency, $post_data,  $this->config['connect_from_localhost']);

            return $validation;
        }   
    }
}