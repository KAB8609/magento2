<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize banner edit page tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('banner_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Information'));
    }
}
