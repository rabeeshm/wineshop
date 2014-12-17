<?php

//require_once 'Mage/Checkout/controllers/CartController.php';

class Confianz_Shipping_ShippingController extends Mage_Core_Controller_Front_Action {

    public function ajaxAction() {
        $carrierObject = Mage::getSingleton('confianz_shipping/carrier');
        $result = array();
        if ($_POST['postalCode'] != 0) {
            $result = $carrierObject->setPickupPointShipping($_POST['postalCode'], 'shippingguid');
        }
        echo $block = $this->getLayout()
                ->createBlock('core/template')
                ->setData('result', $result)
                ->setTemplate('checkout/pickuppoints.phtml')->toHtml();
    }
    /**
     * 
     */
    public function saveKeyAction() {
        Mage::getSingleton('core/session')->setPickupointKey($_POST['key']);
    }
    public function saveMethodAction(){
        Mage::getSingleton('core/session')->setSelectedMethod($_POST['key']);
    }

}
