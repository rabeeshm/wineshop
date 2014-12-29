<?php

/**
 * Carrier model class
 * @author Rabeesh <rabeesh@confianzit.biz>
 */
class Confianz_Shipping_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_MIN_PRODUCT_NUMBER = 'carriers/confianz_shipping/min_product_number';
//    const XML_SHIPPINGGUID_API_URL = 'carriers/confianz_shipping/shippingguide_api_url';
    const XML_PICKUP_API_URL = 'carriers/confianz_shipping/pickup_api_url';
    const XML_CLIENT_URL = 'carriers/confianz_shipping/client_url';
    const XML_SHIPPING_FROM_PIN = 'carriers/confianz_shipping/shipping_from_pin';
    const XML_HOME_DELIVERY_PRICE = 'carriers/confianz_shipping/home_delivery_price';
    const XML_PICKUPPOINT_DELIVERY_PRICE = 'carriers/confianz_shipping/pickuppoint_delivery_price';

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getMinProductNum() {
        return Mage::getStoreConfig(self::XML_MIN_PRODUCT_NUMBER);
    }

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
//    public function getShippingguideApiUrl() {
//        return Mage::getStoreConfig(self::XML_SHIPPINGGUID_API_URL);
//    }

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

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getHomedeliveryPrice() {
        return Mage::getStoreConfig(self::XML_HOME_DELIVERY_PRICE);
    }
    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getPickupPointPrice() {
        return Mage::getStoreConfig(self::XML_PICKUPPOINT_DELIVERY_PRICE);
    }

}
