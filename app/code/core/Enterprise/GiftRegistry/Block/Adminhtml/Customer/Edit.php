<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        $this->_blockGroup = 'Enterprise_GiftRegistry';
        $this->_controller = 'adminhtml_customer';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        $confirmMessage = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Are you sure you want to delete this gift registry?');
        $this->_updateButton('delete', 'label', Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Delete Registry'));
        $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $entity = Mage::registry('current_giftregistry_entity');
        if ($entity->getId()) {
            return $this->escapeHtml($entity->getTitle());
        }
        return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Gift Registry Entity');
    }

    /**
     * Retrieve form back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $customerId = null;
        if (Mage::registry('current_giftregistry_entity')) {
            $customerId = Mage::registry('current_giftregistry_entity')->getCustomerId();
        }
        return $this->getUrl('*/customer/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }
}
