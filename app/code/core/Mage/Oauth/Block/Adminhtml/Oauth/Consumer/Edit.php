<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * OAuth Consumer Edit Block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Consumer model
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_model;

    /**
     * Get consumer model
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getModel()
    {
        if (null === $this->_model) {
            $this->_model = Mage::registry('current_consumer');
        }
        return $this->_model;
    }

    /**
     * Construct edit page
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Mage_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('Mage_Oauth_Helper_Data')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 100);

        $this->_formScripts[] = "function saveAndContinueEdit()" .
        "{editForm.submit($('edit_form').action + 'back/edit/')}";

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', $this->__('Delete'));

        /** @var $auth Mage_Core_Model_Authorization */
        $auth = Mage::getSingleton('Mage_Core_Model_Authorization');
        if (!$this->getModel() || !$this->getModel()->getId() || !$auth->isAllowed('Mage_Oauth::consumer_delete')) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getModel()->getId()) {
            return $this->__('Edit Consumer');
        } else {
            return $this->__('New Consumer');
        }
    }
}
