<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_User role block
 *
 * @category   Mage
 * @package    Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_User_Block_Role extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_controller = 'user_role';

    /**
     * @var string
     */
    protected $_blockGroup = 'Mage_User';


    public function __construct()
    {
        $this->_headerText = Mage::helper('Mage_User_Helper_Data')->__('Roles');
        $this->_addButtonLabel = Mage::helper('Mage_User_Helper_Data')->__('Add New Role');
        parent::__construct();
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
        return Mage_Backend_Block_Widget_Container::_prepareLayout();
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
