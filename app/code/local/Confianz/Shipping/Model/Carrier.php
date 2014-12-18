<?php

/**
 * Carrier model class
 * @author Rabeesh <rabeesh@confianzit.biz>
 */
class Confianz_Shipping_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    /**
     * Carrier's code, as defined in parent class
     *
     * @var string
     */
    protected $_code = 'confianz_shipping';

    /**
     * All allowed methods
     *
     * @var array
     */
    protected $_allowedMethods;

    /**
     * Returns available shipping rates for Confianz Shipping carrier
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');
        
        Mage::getSingleton('core/session')->setPickupointKey();
        
        //set company shipping price
        $result->append($this->_getCompanyRate());
        
        //get product total quantity
        $totalQuantity = Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
        $result->append($this->_getPickupPoints($totalQuantity));

        //set home shipping price
        $result->append($this->_getHomeRate());

        //get billing information
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $zip = $shippingAddress->getPostcode();
        $pickPoints = Mage::getSingleton('core/session')->getPickPoints();
        if (empty($pickPoints)) {
            $flag = 'pickuppoint';
            $pickxml = $this->getBringAPIData($zip, $flag);
            $pickupPoints = array();
            foreach ($pickxml->pickupPoint as $element) {
                $element = $this->xml2array($element);
                $data = array();
                $data['id'] = $element['id'];
                $data['name'] = $element['name'];
                $data['postalCode'] = $element['postalCode'];
                $data['address'] = $element['address'];
                $data['city'] = $element['city'];
                $pickupPoints[] = $data;
            }
            Mage::getSingleton('core/session')->setPickPoints($pickupPoints);
        }
        return $result;
    }

    /**
     * 
     * @param type $zip
     * @param type $flag
     * @return type
     */
    public function getBringAPIData($zip, $flag) {

        if ($flag == 'shippingguid') {
            $url = Mage::helper('confianz_shipping')->getShippingguideApiUrl();
            $clienturl = Mage::helper('confianz_shipping')->getClientUrl();
            $fromPin = Mage::helper('confianz_shipping')->getShippingFromPin();
            $api_url = $url . "?clientUrl= " . $clienturl . "&from=" . $fromPin . "&to=" . $zip . "&weightInGrams0=1500&fromCountry=DK&toCountry=DK&product=CARRYON_HOMESHOPPING_DENMARK";
        } else {
            $url = Mage::helper('confianz_shipping')->getPickupApiUrl();
            //$api_url = "https://api.bring.com/pickuppoint/api/pickuppoint/DK/postalCode/9000.xml";
            $api_url = $url . '/' . $zip . '.xml';
        }
        return simplexml_load_file($api_url);
    }

    /**
     * Returns Allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        return $this->_allowedMethods;
    }

    /**
     * Get Home delivery rate object
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getHomeRate() {
        $this->_allowedMethods[] = array(
            'home' => Mage::helper('checkout')->__('Delivery to home address'),
        );
        $price = Mage::helper('confianz_shipping')->getHomedeliveryPrice();
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('delivery_to_home');
        $rate->setMethodTitle(Mage::helper('checkout')->__('Delivery to home address'));
        $rate->setPrice($price);
        $rate->setCost(0);
        return $rate;
    }

    /**
     * Get Company delivery rate object
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getCompanyRate() {

        $this->_allowedMethods[] = array(
            'company' => Mage::helper('checkout')->__('Delivery to company address'),
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('method_company');
        $rate->setMethodTitle(Mage::helper('checkout')->__('Delivery to company address'));
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    /**
     * 
     * @param type $zip
     * @param type $flag
     * @return type
     */
    public function setPickupPointShipping($zip, $flag) {
        //$result = Mage::getModel('shipping/rate_result');
        $ratexml = $this->getBringAPIData($zip, $flag);
        $pickupPoints = array();
        foreach ($ratexml->Product as $element) {
            $shippingMethod = $element->ProductId . '_pickuppoint';
            $data = array();
            $data['code'] = $this->_code;
            $data['carrierTitle'] = $this->getConfigData('title');
            $data['method'] = $shippingMethod;
            $data['methodTitle'] = $element->GuiInformation->DisplayName;
            $data['price'] = $element->Price->PackagePriceWithAdditionalServices->AmountWithVAT;
            //$data['price'] = 20;
            $data['zip'] = $zip;
            //setting session variable names
            $setPricevar = "setPrice" . $shippingMethod . '_' . $zip;
            $setTitlevar = "setTitle" . $shippingMethod . '_' . $zip;
            $priceElements = $this->xml2array($element->Price->PackagePriceWithAdditionalServices->AmountWithVAT);
            $titleElements = $this->xml2array($element->GuiInformation->DisplayName);
            // save price and title to session
            Mage::getSingleton('core/session')->$setPricevar($priceElements[0]);
            Mage::getSingleton('core/session')->$setTitlevar($titleElements[0]);
            $pickupPoints[] = $data;
        }

        return $pickupPoints;
    }

    /**
     * 
     * @param type $element
     * @return type
     */
    function _getPickupPoints($totalQuantity) {
        $this->_allowedMethods[] = array(
            $element->ProductId . '_pickuppoint' => Mage::helper('checkout')->__('Delivery to pickup point'),
        );
        $price = Mage::helper('confianz_shipping')->getPickupPointPrice();
        //Mage::log(print_r($price, true), null, 'shipping-price.log', true);
        $minNumber = Mage::helper('confianz_shipping')->getMinProductNum();
        //Mage::log(print_r($minNumber, true), null, 'shipping-price.log', true);
        if($totalQuantity >= $minNumber ) {
           $price = 0; 
        }
        
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('delivery_to_pickuppoint');
        $rate->setMethodTitle(Mage::helper('checkout')->__('Delivery to pickup point'));
        $rate->setPrice($price);
        $rate->setCost(0);
        return $rate;
    }

    public function getCode() {
        return $this->_code;
    }

    /**
     * 
     * @return type
     */
    function _getDefualtPickuppoint() {
        $this->_allowedMethods[] = array(
            'defualt_pickuppoint' => "Defualt Pickuppoint",
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        //if($rate->getRate()
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('defualt_pickuppoint');

        $getVar = Mage::getSingleton('core/session')->getPickupointKey();
        //Mage::log("after", null, 'shipping-price.log', true);
        //Mage::log(print_r($getVar, true), null, 'shipping-price.log', true);
        $getTitleVar = "getTitle" . $getVar;
        $getPriceVar = "getPrice" . $getVar;
        $title = Mage::getSingleton('core/session')->$getTitleVar();

        if (!empty($title)) {
            $rate->setMethodTitle($title);
        } else {
            $rate->setMethodTitle('Defualt Pickuppoint');
        }
        $price = Mage::getSingleton('core/session')->$getPriceVar();
        //Mage::log(print_r($price, true), null, 'shipping-price.log', true);
        if (!empty($price) && $price != -1) {
            $rate->setPrice($price);
        } else {
            $rate->setPrice(-1);
        }
        $rate->setCost(0);
        return $rate;
    }

    /**
     * 
     * @param type $xmlObject
     * @param type $out
     * @return type
     */
    function xml2array($xmlObject, $out = array()) {
        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = ( is_object($node) ) ? xml2array($node) : $node;
        }
        return $out;
    }

}
