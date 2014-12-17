<?php
/**
 * Our class name should follow the directory structure of
 * our Observer.php model, starting from the namespace,
 * replacing directory separators with underscores.
 * 
 *                     
 */
class Wineshop_UpdateCartPrice_Model_Observer
{
    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     */
	 public function addComboPrice(Varien_Event_Observer $observer)
    {
    	
    	$no_of_combo_items = 18;
		$session= Mage::getSingleton('checkout/session');
		$wine_count = 0;
		$packet_count = 0;
		foreach($session->getQuote()->getAllItems() as $item)
		{
			
			$productid = $item->getProductId();
			$productsku = $item->getSku();
			$productname = $item->getName();
			$productqty = $item->getQty();
			
			$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
			$value = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
			
			if($value == 'Wine'){
				
				if($productqty > 1){
					$wine_count = $wine_count + $productqty;
				}else{
					$wine_count = $wine_count+1;
				}
			}else if($value == 'Packet'){
				if($productqty > 1){
					$packet_count = $packet_count + $productqty;
				}else{
					$packet_count = $packet_count+1;
				}
			}
		}
		$cart = $observer->getData('cart');		
		$quote = $cart->getData('quote');			
		$items = $quote->getAllVisibleItems();
		
		if($wine_count >= $no_of_combo_items){
			
			foreach($items as $item)
			{
				$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
				$combo_price = $_product->getResource()->getAttribute('combo_price')->getFrontend()->getValue($_product);
				
				$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
				$value = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
					
				if($value == 'Wine'){
					 $item->setPrice($combo_price);
				     $item->setCustomPrice($combo_price); 
				     $item->calcRowTotal();  
				     $item->setOriginalCustomPrice($combo_price);
    				 $item->getProduct()->setIsSuperMode(true);
				     $item->save();  //Update the item
				}
			}
			//$quote->setTotalsCollectedFlag(true)->collectTotals();
			//$quote->save(); //Save cart
    	}else if($wine_count < $no_of_combo_items){
    		foreach($items as $item)
    		{
    			$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
    			$original_price = $_product->getResource()->getAttribute('price')->getFrontend()->getValue($_product);
    			 
    			$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
    			$value = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
    			 
    			if($value == 'Wine'){
	    			$item->setPrice($original_price);
	    			$item->setCustomPrice($original_price);
	    			$item->calcRowTotal();
	    			$item->setOriginalCustomPrice($original_price);
	    			$item->getProduct()->setIsSuperMode(true);
	    			$item->save();  //Update the item
    			}
    		}
    	}
       if($packet_count >= $no_of_combo_items){
	    	
	    	foreach($items as $item)
	    	{
	    		$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
	    		$combo_price = $_product->getResource()->getAttribute('combo_price')->getFrontend()->getValue($_product);
	    
	    		$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
	    		$value = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
	    			
	    		if($value == 'Packet'){
	    			$item->setPrice($combo_price); 
	    			$item->setCustomPrice($combo_price); 
	    			$item->calcRowTotal(); 
	    			$item->setOriginalCustomPrice($combo_price);
	    			$item->getProduct()->setIsSuperMode(true);
	    			$item->save();  //Update the item
	    		}
	    	}
      }else if($packet_count < $no_of_combo_items){
      	foreach($items as $item)
      	{
      		$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
      		$original_price = $_product->getResource()->getAttribute('price')->getFrontend()->getValue($_product);
      		 
      		$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()) ;
      		$value = $_product->getResource()->getAttribute('product_type')->getFrontend()->getValue($_product);
      	
      		if($value == 'Packet'){
      			$item->setPrice($original_price);
      			$item->setCustomPrice($original_price);
      			$item->calcRowTotal();
      			$item->setOriginalCustomPrice($original_price);
      			$item->getProduct()->setIsSuperMode(true);
      			$item->save();  //Update the item
      		}
      	}
      }
      
      $quote->setTotalsCollectedFlag(true)->collectTotals();
      $quote->save(); //Save cart
    }
}