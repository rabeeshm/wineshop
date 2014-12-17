<?php

class Confianz_BringshippingMethod_Carrier_Bring extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling');
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        if ($show) { // This if condition is just to demonstrate how to return success and error in shipping methods
            $i = 1;
            while ($i <= 3) {
                $rate = Mage::getModel('shipping/rate_result_method');

                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod('large');
                $rate->setMethodTitle('Standard delivery');
                $rate->setPrice(1.23);
                $rate->setCost(0);
                $i++;  /* the printed value would be
                  $i before the increment
                  (post-increment) */
            }
        } else {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('name'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }
        return $result;
    }

    public function getAllowedMethods() {
        return array('bring' => $this->getConfigData('name'));
    }

}
