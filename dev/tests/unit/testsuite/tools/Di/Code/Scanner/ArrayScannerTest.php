<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Di_Code_Scanner_ArrayScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\ArrayScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\ArrayScanner();
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../') . '/_files');
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities(array($this->_testDir . '/additional.php'));
        $expected = array(
            'Some_Model_Proxy',
            'Some_Model_EntityFactory'
        );
        $this->assertEquals($expected, $actual);
    }
}
