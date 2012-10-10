<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Toolbar_ExitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Buttons
     */
    protected $_block;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->_block = Mage::getModel(
            'Mage_DesignEditor_Block_Toolbar_Buttons',
            array('data' => array('template' => 'toolbar/exit.phtml'))
        );
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetExitUrl()
    {
        $expected = 'http://localhost/index.php/backend/admin/system_design_editor/exit/';
        $this->assertContains($expected, $this->_block->getExitUrl());
    }
}
