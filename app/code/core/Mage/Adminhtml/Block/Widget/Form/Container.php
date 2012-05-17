<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml form container block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';
    protected $_formScripts = array();
    protected $_formInitScripts = array();
    protected $_mode = 'edit';
    protected $_blockGroup = 'Mage_Adminhtml';

    public function __construct()
    {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('Mage_Adminhtml::widget/form/container.phtml');
        }

        $this->_addButton('back', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        $this->_addButton('reset', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reset'),
            'onclick'   => 'setLocation(window.location.href)',
        ), -1);

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('Mage_Adminhtml_Helper_Data')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('save', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);
    }

    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode
            && !$this->_layout->getChildName($this->_nameInLayout, 'form')
        ) {
            $this->setChild(
                'form',
                $this->getLayout()->createBlock($this->_blockGroup
                    . '_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller . '_' . $this->_mode)))
                    . '_Form'
                )
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * Get form save URL
     *
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getFormActionUrl();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_controller . '/save');
    }

    public function getFormHtml()
    {
        $this->getChildBlock('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }

    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

    public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-' . strtr($this->_controller, '_', '-');
    }

    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    /**
     * Set data object and pass it to form
     *
     * @param Varien_Object $object
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    public function setDataObject($object)
    {
        $this->getChildBlock('form')->setDataObject($object);
        return $this->setData('data_object', $object);
    }

}
