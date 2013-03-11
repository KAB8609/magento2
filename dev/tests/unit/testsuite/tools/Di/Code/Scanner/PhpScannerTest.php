<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/ScannerInterface.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/FileScanner.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/PhpScanner.php';

class Magento_Tools_Di_Code_Scanner_PhpScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\PhpScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var array
     */
    protected $_testFiles = array();

    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\PhpScanner();
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../') . '/_files');
        $this->_testFiles = array(
            $this->_testDir . '/app/code/Mage/SomeModule/Helper/Test.php',
            $this->_testDir . '/app/code/Mage/SomeModule/Model/Test.php',
            $this->_testDir . '/app/bootstrap.php',
        );
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Mage_SomeModule_ElementFactory',
            'Mage_SomeModule_Element_Proxy',
            'Mage_SomeModule_BlockFactory',
            'Mage_SomeModule_ModelFactory',
            'Mage_SomeModule_Model_BlockFactory',
            'Mage_Bootstrap_ModelFactory',
        );
        $this->assertEquals($expected, $actual);
    }
}