<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Logging_Block_Adminhtml_Grid_Filter_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Logging_Block_Adminhtml_Grid_Filter_Ip
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Logging_Block_Adminhtml_Grid_Filter_Ip');
    }

    public function testGetCondition()
    {
        $condition = $this->_block->getCondition();
        $this->assertArrayHasKey('field_expr', $condition);
        $this->assertArrayHasKey('like', $condition);
    }

    public function testGetConditionWithLike()
    {
        $this->_block->setValue('127');
        $condition = $this->_block->getCondition();
        $this->assertContains('127', (string) $condition['like']);
        $this->assertNotEquals('127', (string) $condition['like']); // DB-depended placeholder symbols were added
    }
}