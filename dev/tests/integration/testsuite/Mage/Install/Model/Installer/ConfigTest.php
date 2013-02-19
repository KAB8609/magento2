<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_Installer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir = '';

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Mage::getBaseDir(Mage_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . __CLASS__;
        mkdir(self::$_tmpDir);
    }

    public static function tearDownAfterClass()
    {
        Varien_Io_File::rmdirRecursive(self::$_tmpDir);
    }

    public function testInstall()
    {
        file_put_contents(self::$_tmpDir . '/local.xml.template', "test; {{date}}; {{base_url}}; {{unknown}}");
        $expectedFile = self::$_tmpDir . '/local.xml';

        $config = $this->getMock('Mage_Core_Model_Config', array('getDistroBaseUrl'), array(), '', false);
        $config->expects($this->once())->method('getDistroBaseUrl')->will($this->returnValue('http://example.com/'));
        $expectedContents = "test; <![CDATA[d-d-d-d-d]]>; <![CDATA[http://example.com/]]>; {{unknown}}";
        $dirs = new Mage_Core_Model_Dir(
            Mage::getObjectManager()->get('Magento_Filesystem'),
            self::$_tmpDir,
            array(),
            array(Mage_Core_Model_Dir::CONFIG => self::$_tmpDir)
        );

        $this->assertFileNotExists($expectedFile);
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $model = Mage::getModel('Mage_Install_Model_Installer_Config', array(
            'config' => $config, 'dirs' => $dirs, 'filesystem' => $filesystem
        ));
        $model->install();
        $this->assertFileExists($expectedFile);
        $this->assertStringEqualsFile($expectedFile, $expectedContents);
    }

    public function testGetFormData()
    {
        /** @var $model Mage_Install_Model_Installer_Config */
        $model = Mage::getModel('Mage_Install_Model_Installer_Config');
        /** @var $result Varien_Object */
        $result = $model->getFormData();
        $this->assertInstanceOf('Varien_Object', $result);
        $data = $result->getData();
        $this->assertArrayHasKey('db_host', $data);
    }
}
