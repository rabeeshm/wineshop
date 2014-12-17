<?php


/**
 * Digg link widget
 *
 * @category    Sample
 * @package     Sample_WidgetOne
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wineshop_Regions_Block_Digg
    extends Mage_Core_Block_Abstract
{

    /**
     * Produces digg link html
     *
     * @return string
     */
	protected function _prepareLayout() {
		/*if ($head = $this->getLayout()->getBlock('head')) {
			$head->addCss('jqvmap.css');
			$head->addJs('maps/jquery.vmap.usa.js');
			$head->addJs('jquery.vmap.js');
			$head->addJs('jquery-1.10.2.min.js');*/
		if ($head = $this->getLayout()->getBlock('head')) {
           // echo "GOT IT";    
         /*   $head->addCss('jqvmap.css');
            $head->addItem('js','jquery.vmap.usa.js');
            $head->addJs('jquery.vmap.js');*/
        }
        return parent::_prepareLayout();
	}
    protected function _toHtml()
    {
        //return '<div id="vmap" style="width: 600px; height: 400px;">WIDGET IMAGE</div>';
        $pageTitle = '';
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $pageTitle = $headBlock->getTitle();
        }

        $html = '<a class="delicious" href="'
            . 'http://del.icio.us/post?url='
            . $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true))
            . '" onclick="window.open(\'http://del.icio.us/post?v=4&amp;noui&amp;jump=close&amp;url=\'+encodeURIComponent(\''
            . $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true))
            . "')+'&amp;title='+encodeURIComponent('"
            . $pageTitle
            . "'),'delicious', 'toolbar=no,width=700,height=400'); return false;"
            . '" title="Add to del.icio.us">Del.icio.us</a>';

        return $html;
    }

}
