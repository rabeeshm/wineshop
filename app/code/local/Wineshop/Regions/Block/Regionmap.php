<?php

/**
 * Digg link widget
 *
 * @category    Sample
 * @package     Sample_WidgetOne
 * @author      rabeesh@confianzit.biz
 */
class Wineshop_Regions_Block_regionmap extends Mage_Core_Block_Abstract {

    /**
     * Produces digg link html
     *
     * @return string
     */
    protected function _prepareLayout() {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addCss('jqvmap/jqvmap.css');
            $head->addJs('jqvmap/jquery.vmap.js');
            $head->addJs('jqvmap/include.js');
            //$categoryID = (int) $this->getRequest()->getParam('cat', false);     
            $categoryId = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
            $child_category = Mage::getModel("catalog/category")->load($categoryId);
		$rootparent = Mage::getModel("catalog/category")->load($child_category->getParentId());
            //$parent_category_name = Mage::getModel("catalog/category")->load($child_category->getParentId());
            $_category_code_parent = Mage::getModel('catalog/category')->load($child_category->getParentId())->getData('regioncode');
            $_category_code_child = Mage::getModel('catalog/category')->load($categoryId)->getData('regioncode');
	    $root_category_code_child = Mage::getModel('catalog/category')->load($rootparent->getParentId())->getData('regioncode');
            if (strtolower($_category_code_parent) == 'it' || strtolower($_category_code_child) == 'it' || strtolower($root_category_code_child) == 'it') {
                $head->addJs('jqvmap/italy.js');
                $head->addJs('jqvmap/maps/jquery.vmap.italy.js');
            } else if (strtolower($_category_code_parent) == 'fr' || strtolower($_category_code_child) == 'fr' || strtolower($root_category_code_child) == 'fr') {
                $head->addJs('jqvmap/france.js');
                $head->addJs('jqvmap/maps/jquery.vmap.france.js');
            } else if (strtolower($_category_code_parent) == 'de' || strtolower($_category_code_child) == 'de' || strtolower($root_category_code_child) == 'de') {
                $head->addJs('jqvmap/germany.js');
                $head->addJs('jqvmap/maps/jquery.vmap.germany.js');
            } else {
                $head->addJs('jqvmap/maps/jquery.vmap.italy.js');
                $head->addJs('jqvmap/italy.js');
            }
            return parent::_prepareLayout();
        }
    }

    protected function _toHtml() {
        $html = '';
        //$categoryID = (int) $this->getRequest()->getParam('cat', false);
        $categoryId = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
        $_category_code = Mage::getModel('catalog/category')->load($categoryId)->getData('regioncode');
        //if (!empty($_category_code) || ) {
            $html = '<div id="vmap" style="height: 200px;"></div>';
            echo '<script type="text/javascript">markSelected("' . $_category_code . '");</script>';
        //}
        return $html;
    }

}
