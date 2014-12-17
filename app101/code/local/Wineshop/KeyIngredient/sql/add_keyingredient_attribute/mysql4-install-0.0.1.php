<?php
$this->startSetup();
$this->addAttribute('catalog_product', 'keyingredient', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => 'varchar',
    'label'         => 'Key Ingredients',
    'backend'       => '',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => false,
	'sort_order' =>     '4',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
$this->endSetup();