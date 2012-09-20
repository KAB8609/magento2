<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Logging_Block_Adminhtml_Grid_Filter_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip
     */
    protected $_block;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $this->_block= new Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip();
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetCondition()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $condition = $this->_block->getCondition();
        $this->assertArrayHasKey('field_expr', $condition);
        $this->assertArrayHasKey('like', $condition);
    }

    public function testGetConditionWithLike()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $this->_block->setValue('127');
        $condition = $this->_block->getCondition();
        $this->assertContains('127', (string) $condition['like']);
        $this->assertNotEquals('127', (string) $condition['like']); // DB-depended placeholder symbols were added
    }
}
