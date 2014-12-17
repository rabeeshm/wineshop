<?php
class Confianz_Shipping_Model_Carrier_LocalDelivery extends Mage_Shipping_Model_Carrier_Abstract
{
    /* Use group alias */
    protected $_code = 'glsshipping';
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
      
        // skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
            return false;
 
        $result = Mage::getModel('shipping/rate_result');
        $handling = 0;
#        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling') >0)
#            $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
#        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling_type') == 'P' && $request->getPackageValue() > 0)
#            $handling = $request->getPackageValue()*$handling;
            
        $wine_qty=0;
        $pack_qty=0;
        
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
            //check is packet
              $_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
              //print_r($_product);
              $product_type = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
              if($product_type == 'Packet')
                   $pack_qty += $item->getQty();
               else if($product_type == 'Wine'){
                  $wine_qty += $item->getQty();
               }
                
            }     
        }
        if($wine_qty > 0 &&  $wine_qty <= 12)
           $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
        elseif($wine_qty > 12 &&  $wine_qty <= 23)
           $handling = 20 + Mage::getStoreConfig('carriers/'.$this->_code.'/handling'); 
//        elseif($wine_qty > 23 &&  $wine_qty <= 53)  
//           $handling = 3 * Mage::getStoreConfig('carriers/'.$this->_code.'/handling');   
        else
             $handling = 0;    
         
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
        /* Use method name */
        $method->setMethod('delivery');
        $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));
        $method->setCost($handling);
        $method->setPrice($handling);
        $result->append($method);
        return $result;
    }
    
    public function _doShipmentRequest(Varien_Object $request){ }
}
