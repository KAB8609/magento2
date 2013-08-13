<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Design tab with cms page attributes and some modifications to CE version
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Design
    extends Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design
{
    /**
     * Adding onchange js call
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Design
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        Mage::helper('Enterprise_Cms_Helper_Data')->addOnChangeToFormElements($this->getForm(), 'dataChanged();');

        return $this;
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'Mage_Cms::save') {
            $action = 'Enterprise_Cms::save_revision';
        }
        return parent::_isAllowedAction($action);
    }
}
