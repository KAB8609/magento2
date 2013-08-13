<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rating Observer Model
 *
 * @category   Mage
 * @package    Magento_Rating
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Observer
{
    /**
     * Cleanup product ratings after product delete
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Rating_Model_Observer
     */
    public function processProductAfterDeleteEvent(Magento_Event_Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('Magento_Rating_Model_Resource_Rating')
                ->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
