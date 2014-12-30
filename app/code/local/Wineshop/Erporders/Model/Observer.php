<?php

/**
 * Magento
 * @category    ERP Sales
 * @package     Erpsales_Erporders
 *
 * @author rabeesh@confianzit.biz 
 *
 */
class Wineshop_Erporders_Model_Observer extends Mage_Sales_Model_Order_Api {

    public function __construct() {
        
    }

    /**
     * Export Order Details into Oper Erp module.
     * @author rabeesh@confianzit.biz 
     * @param Varien_Object $observer
     * @return Wineshop_Erporders_Model_Observer
     */
    public function exportOrderToErp(Varien_Event_Observer $observer) {
        //$observer contains the object returns in the event.
        $order = $observer->getEvent()->getOrder();
        $order_code = Mage::getModel("sales/order")->getCollection()->getLastItem()->getIncrementId();
        //$store_id = Mage::app()->getStore()->getData('store_id');
        $website = Mage::app()->getWebsite()->getCode();
        $orderIncrementId = $order_code;
        $store = Mage::getModel('core/store')->load($order->store_id);

        //Function call request for import_order_from_magento().
        $selected_method = Mage::getSingleton('core/session')->getSelectedPickupoint();
        //Mage::log(print_r($selected_method, true), null, 'shipment.log', true); exit;
        $selected_method = explode("-", $selected_method);
        $result['pickup_point']['id'] = $selected_method[1];
        $result['pickup_point']['pinCode'] = $selected_method[0];
        $result['result'] = $this->info($order_code);


        //Getting stored configurations..
        $server = Mage::getStoreConfig('erporders/erpconfig/servername');
        $port = Mage::getStoreConfig('erporders/erpconfig/port');
        $db = Mage::getStoreConfig('erporders/erpconfig/dbname');
        $user = Mage::getStoreConfig('erporders/erpconfig/username');
        $pwd = Mage::getStoreConfig('erporders/erpconfig/password');

        //ini_set('error_reporting', E_ALL);
        //ini_set('display_errors', 1);
        $errors = array();
        // change it to suit your RPC service
        $client = new Zend_XmlRpc_Client($server . ':' . $port . '/xmlrpc/common');
        $client_object = new Zend_XmlRpc_Client($server . ':' . $port . '/xmlrpc/object');
        try {
            //Function call request for login.
            $login_param = array(
                'dbname' => $db,
                'username' => $user,
                'password' => $pwd,
            );
            $uid = $client->call('login', $login_param);

            //Mage::log(print_r($result, true), null, 'shipment.log', true); //exit;
            $params = array($db, $uid, $pwd, 'sale.order', 'import_order_from_magento', $website, $order_code, $store->group_id, $result);
            $client_object->call('execute', $params);
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        } catch (Zend_XmlRpc_Client_HttpException $e) {
            $errors[] = '[' . $e->getCode() . ']:' . $e->getMessage();
        }
        ### print out errors if any 
        if ($errors) {
            //print_r($errors);
        }
        return $this;
    }

}
