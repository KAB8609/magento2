<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/ScannerInterface.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/Di/Code/Scanner/XmlInterceptorScanner.php';

class Magento_Tools_Di_Code_Scanner_XmlInterceptorScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\XmlInterceptorScanner
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
        $this->_model = new Magento\Tools\Di\Code\Scanner\XmlInterceptorScanner();
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../') . '/_files');
        $this->_testFiles =  array(
            $this->_testDir . '/app/code/Mage/SomeModule/etc/config.xml',
            $this->_testDir . '/app/etc/config.xml',
        );
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Mage_Core_Model_Cache_Interceptor',
            'Mage_Core_Controller_Varien_Action_Context_Interceptor',
        );
        $this->assertEquals($expected, $actual);
    }
}