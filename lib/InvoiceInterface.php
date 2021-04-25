<?php

namespace Sslcommerz\Invoice;

interface InvoiceInterface
{
    public function InitiatePaymentLink(array $data);

    public function setParams($data);

    public function setRequiredInfo(array $data);

    public function setCustomerInfo(array $data);

    public function setShipmentInfo(array $data);

    public function setProductInfo(array $data);

    public function setAdditionalInfo(array $data);

    public function callToApi($data, $setLocalhost = false);
}