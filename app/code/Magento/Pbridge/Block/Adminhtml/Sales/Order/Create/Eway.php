<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Eway.Com.Au payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Eway
    extends Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Cybersource payment code
     *
     * @var string
     */
    protected $_code = 'eway_direct';
}