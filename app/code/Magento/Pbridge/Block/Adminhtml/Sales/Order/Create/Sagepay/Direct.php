<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sagepay Direct payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Sagepay_Direct
    extends Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Authorize payment code
     *
     * @var string
     */
    protected $_code = 'sagepay_direct';
}