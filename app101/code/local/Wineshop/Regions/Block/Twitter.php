<?php
/**
 * Twitter link widget
 *
 * @category    Sample
 * @package     Sample_WidgetOne
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wineshop_Regions_Block_Twitter
    extends Mage_Core_Block_Abstract
    implements Mage_Cms_Block_Widget_Interface
{

    /**
     * Produces twitter link html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $pageTitle = '';
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $pageTitle = $headBlock->getTitle();
        }

        $html = '<a title="Tweet about this page"'
            . ' href="http://twitter.com/home?status=Currently reading '
            . $pageTitle
            . ' at '
            . $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true))
            . '" target="_blank">Tweet This!</a>';

        return $html;
    }

}
