<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * OAuth Consumer Edit Block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Consumer model
     *
     * @var Magento_Oauth_Model_Consumer
     */
    protected $_model;

    /**
     * Get consumer model
     *
     * @return Magento_Oauth_Model_Consumer
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
        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('Magento_Oauth_Helper_Data')->__('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 100);

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', $this->__('Delete'));

        if (!$this->getModel()
            || !$this->getModel()->getId()
            || !$this->_authorization->isAllowed('Magento_Oauth::consumer_delete')
        ) {
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
