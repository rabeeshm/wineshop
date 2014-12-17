<?php

//require_once 'Mage/Checkout/controllers/CartController.php';

class Confianz_Shipping_ShippingController extends Mage_Core_Controller_Front_Action {

    public function ajaxAction() {

        $carrierObject = Mage::getSingleton('confianz_shipping/carrier');
        $result = $carrierObject->setPickupPointShipping($_POST['postalCode'], 'shippingguid');
        
        //$this->setShippingCost();

        $dat = array("pin" => $_POST['postalCode'], "flag" => true);
        $carrierObject->collectRates($dat);



        echo $block = $this->getLayout()
                ->createBlock('core/template')
                ->setData('result', $result)
                ->setTemplate('checkout/pickuppoints.phtml')->toHtml();
    }

    public function getAllShippingMethods() {
        $carriers = Mage::getStoreConfig('carriers', Mage::app()->getStore()->getId());
        foreach ($carriers as $carrierCode => $carrierConfig) {
            $data[] = $carrierConfig;
        }
        return $data;
    }

    public function setShippingCost() {
        $session = Mage::getSingleton('checkout/session');

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteid = $quote->getId();
        if ($quoteid) {
            try {
                $address = $quote->getShippingAddress();
                if ($address->getAddressType() == 'shipping') {
                    //    echo '<pre>'; print_r($events->getQuoteAddress()->getData()); exit;
                    $price = 40;

                    // Find if our shipping has been included.
                    $rates = $address->collectShippingRates()
                            ->getGroupedAllShippingRates();

                    foreach ($rates as $carrier) {
                        foreach ($carrier as $rate) {
                            $rate->setPrice($price);
                            $rate->save();
                        }
                    }
                    $this->collectTotals($quote, $price);
                }
                $quote->collectTotals();
            } catch (Exception $e) {
                Mage::logException($e);
                $response['error'] = $e->getMessage();
            }
        }
    }

    public function collectTotals($quote, $price) {
        $quoteid = $quote->getId();
        $shippingcode = 'freeshipping_freeshipping_rb';
        if ($quoteid) {
            try {

                $quote->getShippingAddress()->setShippingMethod($shippingcode)/* ->collectTotals() */->save();
                $quote->save();
                foreach ($quote->getAllAddresses() as $address) {
                    $address->setShippingAmount($price);
                    $address->setBaseShippingAmount($price);
                    $address->save();
                }

                $response['message'] = 'Succcess';
            } catch (Exception $e) {
                Mage::logException($e);
                $response['error'] = $e->getMessage();
            }
        }
    }

}
