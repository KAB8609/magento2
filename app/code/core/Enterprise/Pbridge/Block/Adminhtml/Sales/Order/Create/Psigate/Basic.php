<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PSi Gate payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Psigate_Basic
    extends Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
{
    /**
     * Psigate payment code
     *
     * @var string
     */
    protected $_code = 'psigate_basic';
}
