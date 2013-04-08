<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{

    protected $_template = 'customer/form.phtml';

    /**
     * Prepare layout
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Form
     */
    protected function _prepareLayout()
    {
        $this->addChild('entity_items', 'Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Items');
        $this->addChild('cart_items', 'Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Cart');
        $this->addChild('sharing_form', 'Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Sharing');
        $this->addChild('update_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label' => $this->helper('Enterprise_GiftRegistry_Helper_Data')->__('Update Items and Qty\'s'),
            'type'  => 'submit'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve website name
     *
     * @return string
     */
    public function getWebsiteName()
    {
        return Mage::app()->getWebsite($this->getEntity()->getWebsiteId())->getName();
    }

    /**
     * Retrieve owner name
     *
     * @return string
     */
    public function getOwnerName()
    {
        $customer = Mage::getModel('Mage_Customer_Model_Customer')
            ->load($this->getEntity()->getCustomerId());

        return $this->escapeHtml($customer->getName());
    }

    /**
     * Retrieve customer edit form url
     *
     * @return string
     */
    public function getOwnerUrl()
    {
        return $this->getUrl('*/customer/edit', array('id' => $this->getEntity()->getCustomerId()));
    }

    /**
     * Retrieve gift registry type name
     *
     * @return string
     */
    public function getTypeName()
    {
        $type = Mage::getModel('Enterprise_GiftRegistry_Model_Type')
            ->load($this->getEntity()->getTypeId());

        return $this->escapeHtml($type->getLabel());
    }

   /**
     * Retrieve escaped entity title
     *
     * @return string
     */
    public function getEntityTitle()
    {
        return $this->escapeHtml($this->getEntity()->getTitle());
    }

   /**
     * Retrieve escaped entity message
     *
     * @return string
     */
    public function getEntityMessage()
    {
        return $this->escapeHtml($this->getEntity()->getMessage());
    }

   /**
     * Retrieve list of registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        return $this->escapeHtml($this->getEntity()->getRegistrants());
    }

   /**
     * Return gift registry entity object
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('current_giftregistry_entity');
    }

   /**
     * Return shipping address
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getShippingAddressHtml()
    {
        return $this->getEntity()->getFormatedShippingAddress();
    }

   /**
     * Return gift registry creation data
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getCreatedAt()
    {
        return $this->formatDate($this->getEntity()->getCreatedAt(),
            Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true
        );
    }

    /**
     * Return update items form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/update', array('_current' => true));
    }
}
