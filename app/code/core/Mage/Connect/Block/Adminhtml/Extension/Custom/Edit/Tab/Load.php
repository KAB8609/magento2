<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for grid with packages.
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Load
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve Grid Block HTML
     *
     * @return string
     */
    public function getPackageGridHtml()
    {
        return $this->getChildHtml('local_package_grid');
    }
}
