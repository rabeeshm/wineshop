<?php
class Zero1_Cookiealert_Model_Cookiealert extends Mage_Core_Block_Template
{	
	public function getCmsPageUrl()
	{		
		return Mage::getUrl(Mage::getStoreConfig('cookiealert/options/cms_page'));
	}
}