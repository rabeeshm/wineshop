<?php

/**
 * Carrier model class
 * @author Rabeesh <rabeesh@confianzit.biz>
 */
class Confianz_Shipping_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_EXPRESS_MAX_WEIGHT = 'carriers/confianz_shipping/express_max_weight';
    const XML_SHIPPINGGUID_API_URL = 'carriers/confianz_shipping/shippingguide_api_url';
    const XML_PICKUP_API_URL = 'carriers/confianz_shipping/pickup_api_url';
    const XML_CLIENT_URL = 'carriers/confianz_shipping/client_url';
    const XML_SHIPPING_FROM_PIN = 'carriers/confianz_shipping/shipping_from_pin';

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getExpressMaxWeight() {
        return Mage::getStoreConfig(self::XML_EXPRESS_MAX_WEIGHT);
    }

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getShippingguideApiUrl() {
        return Mage::getStoreConfig(self::XML_SHIPPINGGUID_API_URL);
    }

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getPickupApiUrl() {
        return Mage::getStoreConfig(self::XML_PICKUP_API_URL);
    }

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getClientUrl() {
        return Mage::getStoreConfig(self::XML_CLIENT_URL);
    }

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getShippingFromPin() {
        return Mage::getStoreConfig(self::XML_SHIPPING_FROM_PIN);
    }
    
}
