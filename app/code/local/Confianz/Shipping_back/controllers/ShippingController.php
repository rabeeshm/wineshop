<?php

//require_once 'Mage/Checkout/controllers/CartController.php';

class Confianz_Shipping_ShippingController extends Mage_Core_Controller_Front_Action {

    public function ajaxAction() {
        $carrierObject = Mage::getSingleton('confianz_shipping/carrier');
        $result = $carrierObject->setPickupPointShipping($_POST['postalCode'], 'shippingguid');
        
        $dat = array("pin" => $_POST['postalCode'], "flag" => true);
        $carrierObject->collectRates($dat);
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        
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

}
