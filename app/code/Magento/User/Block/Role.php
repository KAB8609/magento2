<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_User role block
 *
 * @category   Mage
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_User_Block_Role extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_controller = 'user_role';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_User';


    protected function _construct()
    {
        $this->_headerText = Mage::helper('Magento_User_Helper_Data')->__('Roles');
        $this->_addButtonLabel = Mage::helper('Magento_User_Helper_Data')->__('Add New Role');
        parent::_construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/editrole');
    }

    protected function _prepareLayout()
    {
        if (!$this->getLayout()->getChildName($this->getNameInLayout(), 'grid')) {
            $this->setChild(
                'grid',
                $this->getLayout()->createBlock(
                    $this->_blockGroup . '_Block_Role_Grid',
                    $this->_controller . '.grid')
                    ->setSaveParametersInSession(true)
            );
        }
        return Magento_Backend_Block_Widget_Container::_prepareLayout();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_role_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
