<?php
/**
 *
 * @author vinod@confianzit.biz 
 *
 */
require_once 'app/Mage.php';

class Confianz_OpenERPConnector_Model_Observer extends Mage_Core_Model_Abstract {

  
    
    public function openerpExportOrder(Varien_Event_Observer $observer)
	{
	   /*
	       Function executed after sales order complete event
	       send the order details to openerp 
	   */
	   
	   //get the order details from observer
	   $orderDetails = $observer->getEvent()->getOrder();
       
       // load the order
       $order = Mage::getModel('sales/order')->load($orderDetails->getId());
       $result = $order->getData();
       
       /*
           if the order is placed by a customer(not a guest)
           append the customer and adress details to result
       */
       if($order->getCustomerId() != NULL){
           $result['customer_data'] = Mage::getModel('customer/customer')->load($order->getCustomerId())->getData();
           $result['shipping_address'] = $order->getShippingAddress()->getData();
           $result['billing_address'] = $order->getBillingAddress()->getData();
           $result['customer_data']['dob']= $order->getCustomerDob();
           $result['customer_data']['taxvat']= $order->getCustomerTaxvat();
       }
       
       /*
         Append the items
       */
       $result['items'] = array();
       foreach ($order->getAllItems() as $item) {
           $result['items'][] = $item->getData();
        }
        $result['payment'] = $order->getPayment()->getData();
        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $history->getData();
        }
      
       
       $store = Mage::getModel('core/store')->load($order->store_id);
    
       $webiste = Mage::getModel('core/website')->load($store->website_id);
           
       /***************************************************/ 
          // this area should come from configuration
	$server = Mage::getStoreConfig('erporders/erpconfig/servername');
        $port = Mage::getStoreConfig('erporders/erpconfig/port');
        $db = Mage::getStoreConfig('erporders/erpconfig/dbname');
        $user = Mage::getStoreConfig('erporders/erpconfig/username');
        $pwd = Mage::getStoreConfig('erporders/erpconfig/password');
	   /***************************************************/ 
	   
	   $login_client = new Zend_XmlRpc_Client($server.':'.$port."/xmlrpc/common"); // openerp common xmlrpc connctor
       $obj_client = new Zend_XmlRpc_Client($server.':'.$port."/xmlrpc/object"); // openerp object xmlrpc connctor
         
       
       $uid = $login_client->call('login', array('db'=>$db, 'login'=>$user, 'password'=>$pwd)); // login to th eopenerp
       if($uid)
           $obj_client->call('execute', array($db, $uid, $pwd, 'sale.order','import_order_from_magento',$webiste->code,$order->increment_id,$store->group_id,$result));  // send th eorder details to openerp
      
       
	}
	
}
