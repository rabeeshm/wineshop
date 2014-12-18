<?php

/**
 * Shipping controller class
 * @author Rabeesh <rabeesh@confianzit.biz>
 */
class Confianz_Shipping_ShippingController extends Mage_Core_Controller_Front_Action {

    public function ajaxAction() {
        $carrierObject = Mage::getSingleton('confianz_shipping/carrier');
        $result = array();
        if ($_POST['postalCode'] != 0) {
            Mage::getSingleton('core/session')->setPickupointKey($_POST['postalCode']);
        }
        //echo $_POST['postalCode'];
//        if ($_POST['postalCode'] != 0) {
//            $result = $carrierObject->setPickupPointShipping($_POST['postalCode'], 'shippingguid');
//        }
//        echo $block = $this->getLayout()
//                ->createBlock('core/template')
//                ->setData('result', $result)
//                ->setTemplate('checkout/pickuppoints.phtml')->toHtml();
    }

    /**
     * saving code name into session
     */
    public function saveKeyAction() {
        Mage::getSingleton('core/session')->setPickupointKey($_POST['key']);
        $this->setShippingCost();
    }

    /**
     * saving method name into session
     */
    public function saveMethodAction() {
        Mage::getSingleton('core/session')->setSelectedMethod($_POST['key']);
    }

    /**
     * set shipping coast.
     */
    public function setShippingCost() {
        $session = Mage::getSingleton('checkout/session');

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteid = $quote->getId();
        if ($quoteid) {
            try {
                $address = $quote->getShippingAddress();
                if ($address->getAddressType() == 'shipping') {

                    $getVar = Mage::getSingleton('core/session')->getPickupointKey();
                    $getPriceVar = "getPrice" . $getVar;
                    $priceAmount = Mage::getSingleton('core/session')->$getPriceVar();
                    Mage::log(print_r($price, true), null, 'shipment1.log', true);
                    if (!empty($priceAmount)) {
                        $price = $priceAmount;
                    } else {
                        $price = -1;
                    }
                    // Find if our shipping has been included.
                    $rates = $address->collectShippingRates()
                            ->getGroupedAllShippingRates();

                    foreach ($rates as $carrier) {
                        foreach ($carrier as $rate) {
                            if ($rate->getCode() == 'confianz_shipping_defualt_pickuppoint') {
                                $rate->setPrice($price);
                                $rate->save();
                            }
                        }
                    }
                    //$this->collectTotals($quote, $price);
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
        $shippingcode = 'confianz_shipping_defualt_pickuppoint';
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
