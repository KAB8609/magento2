<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml products tagged by tag
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Product extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        parent::_construct();

        switch( $this->getRequest()->getParam('ret') ) {
            case 'all':
                $url = $this->getUrl('*/*/');
                break;

            case 'pending':
                $url = $this->getUrl('*/*/pending');
                break;

            default:
                $url = $this->getUrl('*/*/');
        }

        $this->_block = 'tag_product';
        $this->_controller = 'tag_product';
        $this->_removeButton('add');
        $this->setBackUrl($url);
        $this->_addBackButton();

        $tagInfo = Mage::getModel('Magento_Tag_Model_Tag')
            ->load(Mage::registry('tagId'));

        $this->_headerText = Mage::helper('Magento_Tag_Helper_Data')->__("Products Tagged with '%s'", $this->escapeHtml($tagInfo->getName()));
    }

}
