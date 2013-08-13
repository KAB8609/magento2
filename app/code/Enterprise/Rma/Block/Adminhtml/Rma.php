<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Adminhtml Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize RMA management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'Enterprise_Rma';
        $this->_headerText = Mage::helper('Enterprise_Rma_Helper_Data')->__('Returns');
        $this->_addButtonLabel = Mage::helper('Enterprise_Rma_Helper_Data')->__('New Returns Request');
        parent::_construct();
    }

    /**
     * Get URL for New RMA Button
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}
