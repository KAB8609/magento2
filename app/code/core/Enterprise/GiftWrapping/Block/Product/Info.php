<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping info block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Product_Info extends Mage_Core_Block_Template
{
    /**
     * Return product gift wrapping info
     *
     * @return false|Varien_Object
     */
    public function getGiftWrappingInfo()
    {
        $wrappingId = $this->getLayout()->getBlock('additional.product.info')
            ->getItem()
            ->getGwId();

        if ($wrappingId) {
            return Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->load($wrappingId);
        }
        return false;
    }
}
