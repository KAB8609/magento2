<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for result of catalog product composite update
 * Forms response for a popup window for a case when form is directly submitted
 * for single item
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Update_Result extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = $this->_coreRegistry->registry('composite_update_result');
        $resultJson = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return Mage::helper('Magento_Adminhtml_Helper_Js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}
