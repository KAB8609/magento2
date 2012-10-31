<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Cache extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_controller = 'cache';
        $this->_headerText = Mage::helper('Mage_Core_Helper_Data')->__('Cache Storage Management');
        parent::_construct();
        $this->_removeButton('add');
        $this->_addButton('flush_magento', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('Flush Magento Cache'),
            'onclick'   => 'setLocation(\'' . $this->getFlushSystemUrl() .'\')',
            'class'     => 'delete',
        ));

        $message = Mage::helper('Mage_Core_Helper_Data')->__('Cache storage may contain additional data. Are you sure that you want flush it?');
        $this->_addButton('flush_system', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('Flush Cache Storage'),
            'onclick'   => 'confirmSetLocation(\''.$message.'\', \'' . $this->getFlushStorageUrl() .'\')',
            'class'     => 'delete',
        ));
    }

    /**
     * Get url for clean cache storage
     */
    public function getFlushStorageUrl()
    {
        return $this->getUrl('*/*/flushAll');
    }

    /**
     * Get url for clean cache storage
     */
    public function getFlushSystemUrl()
    {
        return $this->getUrl('*/*/flushSystem');
    }
}
