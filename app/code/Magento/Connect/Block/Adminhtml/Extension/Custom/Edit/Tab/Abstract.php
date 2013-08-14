<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract for extension info tabs
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * TODO
     */
    protected $_addRowButtonHtml;

    /**
     * TODO
     */
    protected $_removeRowButtonHtml;

    /**
     * TODO
     */
    protected $_addFileDepButtonHtml;

    /**
     * TODO
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData(Mage::getSingleton('Magento_Connect_Model_Session')->getCustomExtensionPackageFormData());
    }

    /**
     * TODO   remove ???
     */
    public function initForm()
    {
        return $this;
    }

    /**
     * TODO
     */
    public function getValue($key, $default='')
    {
        $value = $this->getData($key);
        return htmlspecialchars($value ? $value : $default);
    }

    /**
     * TODO
     */
    public function getSelected($key, $value)
    {
        return $this->getData($key)==$value ? 'selected="selected"' : '';
    }

    /**
     * TODO
     */
    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked="checked"' : '';
    }

    /**
     * TODO
     */
    public function getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Widget_Button')
                    ->setType('button')
                    ->setClass('add')
                    ->setLabel($this->__($title))
                    ->setOnClick("addRow('".$container."', '".$template."')")
                    ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    /**
     * TODO
     */
    public function getRemoveRowButtonHtml($selector='span')
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Widget_Button')
                    ->setType('button')
                    ->setClass('delete')
                    ->setLabel($this->__('Remove'))
                    ->setOnClick("removeRow(this, '".$selector."')")
                    ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }

    public function getAddFileDepsRowButtonHtml($selector='span', $filesClass='files')
    {
        if (!$this->_addFileDepButtonHtml) {
            $this->_addFileDepButtonHtml = $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Widget_Button')
                    ->setType('button')
                    ->setClass('add')
                    ->setLabel($this->__('Add files'))
                    ->setOnClick("showHideFiles(this, '".$selector."', '".$filesClass."')")
                    ->toHtml();
        }
        return $this->_addFileDepButtonHtml;

    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return '';
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return '';
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
