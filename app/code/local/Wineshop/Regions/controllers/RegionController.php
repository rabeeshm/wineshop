<?php
/*!
 * Regions controller
 * @author : rabeesh@confianzit.biz
 */
class Wineshop_Regions_RegionController extends Mage_Core_Controller_Front_Action {

    /**
     * Get Category url using category code
     */
    public function getRegionAction() {
        $code = $_REQUEST['code'];
        $order = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('regioncode', $code)
                ->load()
                ->getFirstItem();
        if($order->getId())
            echo Mage::getModel("catalog/category")->load($order->getId())->getUrl();
    }

}
