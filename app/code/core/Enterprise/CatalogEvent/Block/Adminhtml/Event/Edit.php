<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Events edit page
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'id';
    protected $_blockGroup = 'Enterprise_CatalogEvent';
    protected $_controller = 'adminhtml_event';

    /**
     * Prepare catalog event form or category selector
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit
     */
    protected function _prepareLayout()
    {
        if (!$this->getEvent()->getId() && !$this->getEvent()->getCategoryId()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_addButton(
                'save_and_continue',
                array(
                    'label' => $this->helper('Enterprise_CatalogEvent_Helper_Data')->__('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attr'  => array(
                        'widget-button' => array('event' => 'saveAndContinueEdit', 'related' => '#edit_form')
                    )
                ),
                1
            );
        }

        parent::_prepareLayout();

        if (!$this->getEvent()->getId() && !$this->getEvent()->getCategoryId()) {
            $this->setChild(
                'form',
                $this->getLayout()->createBlock($this->_blockGroup
                    . '_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller . '_' . $this->_mode)))
                    . '_Category',
                    $this->getNameInLayout() . 'catalog_event_form'
                )
            );
        }

        if ($this->getRequest()->getParam('category')) {
            $this->_updateButton('back', 'label', $this->helper('Enterprise_CatalogEvent_Helper_Data')->__('Back to Category'));
        }

        if ($this->getEvent()->isReadonly() && $this->getEvent()->getImageReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
            $this->_removeButton('save_and_continue');
        }

        if (!$this->getEvent()->isDeleteable()) {
            $this->_removeButton('delete');
        }

        return $this;
    }


    /**
     * Retrieve form back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRequest()->getParam('category')) {
            return $this->getUrl(
                '*/catalog_category/edit',
                array('clear' => 1, 'id' => $this->getEvent()->getCategoryId())
            );
        } elseif ($this->getEvent() && !$this->getEvent()->getId() && $this->getEvent()->getCategoryId()) {
            return $this->getUrl(
                '*/*/new',
                array('_current' => true, 'category_id' => null)
            );
        }

        return parent::getBackUrl();
    }


    /**
     * Retrieve form container header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEvent()->getId()) {
            return Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Edit Catalog Event');
        }
        else {
            return Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Add Catalog Event');
        }
    }

    /**
     * Retrive catalog event model
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        return Mage::registry('enterprise_catalogevent_event');
    }

}
