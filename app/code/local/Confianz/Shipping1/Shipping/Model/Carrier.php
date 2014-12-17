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
        print_r($request['flag']);
        $result = Mage::getModel('shipping/rate_result');

        /** @var Confianz_Shipping_Helper_Data $expressMaxProducts */
        //$expressMaxWeight = Mage::helper('confianz_shipping')->getExpressMaxWeight();
        //$expressAvailable = true;
//        foreach ($request->getAllItems() as $item) {
//            if ($item->getWeight() > $expressMaxWeight) {
//                $expressAvailable = false;
//            }
//        }
//        if ($expressAvailable) {
//            $result->append($this->_getCompanyRate());
//        }
        
        
        $result->append($this->_getCompanyRate());
        
        if ($request['flag']) {
            $result->append($this->_getPickupPoints1());
        }
        //get billing information
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $zip = $shippingAddress->getPostcode();

        $flag = 'shippingguid';
        $ratexml = $this->getBringAPIData($zip, $flag);
        foreach ($ratexml->Product as $element) {
            $result->append($this->_getHomeRate($element));
        }

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
        //Mage::log(print_r($pickupPoints, true), null, 'shipment.log', true);
        Mage::getSingleton('core/session')->setPickPoints($pickupPoints);



        return $result;
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
    protected function _getHomeRate($element) {

        $this->_allowedMethods[] = array(
            $element->ProductId . '_home' => $element->GuiInformation->DisplayName,
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($element->ProductId . '_home');
        $rate->setMethodTitle($element->GuiInformation->DisplayName);
        $rate->setPrice($element->Price->PackagePriceWithAdditionalServices->AmountWithVAT);
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
            'company' => 'Delivery to company address',
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('method_company');
        $rate->setMethodTitle('Delivery to company address');
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
        $result = Mage::getModel('shipping/rate_result');
        $ratexml = $this->getBringAPIData($zip, $flag);
        $pickupPoints = array();
        foreach ($ratexml->Product as $element) {

            //$result->append($this->_getPickupPoints($element));


            $shippingMethod = $element->ProductId . '_pickuppoint';
            $data = array();
            $data['code'] = $this->_code;
            $data['carrierTitle'] = $this->getConfigData('title');
            $data['method'] = $shippingMethod;
            $data['methodTitle'] = $element->GuiInformation->DisplayName;
            $data['price'] = $element->Price->PackagePriceWithAdditionalServices->AmountWithVAT;
            $pickupPoints[] = $data;

            //$this->getQuote()->getShippingAddress()
            //->setShippingMethod($shippingMethod);
//            $rate = Mage::getSingleton('checkout/type_onepage')->getQuote()
//                    ->getShippingAddress()
//                    ->collectShippingRates()
//                    ->getShippingRateByCode($shippingMethod);
//            $result = Mage::getSingleton('checkout/type_onepage')->getQuote()
//                    ->getShippingAddress()
//                    ->setShippingMethod($shippingMethod);
//            Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
            
            
            
            
            
            
        }

        
        
        
        
        
        

        return $pickupPoints;
    }

    /**
     * 
     * @param type $element
     * @return type
     */
    function _getPickupPoints($element) {
        $this->_allowedMethods[] = array(
            $element->ProductId . '_pickuppoint' => $element->GuiInformation->DisplayName,
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($element->ProductId . '_pickuppoint');
        $rate->setMethodTitle($element->GuiInformation->DisplayName);
        $rate->setPrice($element->Price->PackagePriceWithAdditionalServices->AmountWithVAT);
        $rate->setCost(0);


        return $rate;
    }

    public function getCode() {
        return $this->_code;
    }

    function _getPickupPoints1() {
        $this->_allowedMethods[] = array(
            'pickuppoint' => "Sadsa",
        );
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle('OOOOOOOOOOOOO');
        $rate->setMethod('CARRYON_HOMESHOPPING_DENMARK_pickuppoint');
        $rate->setMethodTitle('TTTTTTTTTTTTTT');
        $rate->setPrice(10);
        $rate->setCost(0);


        return $rate;
    }

}
