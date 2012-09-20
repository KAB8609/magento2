<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testGetUploader()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content', 'block');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Media_Uploader', $block->getUploader());
    }
}
