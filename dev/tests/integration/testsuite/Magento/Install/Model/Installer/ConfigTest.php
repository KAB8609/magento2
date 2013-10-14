<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model\Installer;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir = '';

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')
            ->getDir(\Magento\App\Dir::VAR_DIR) . DIRECTORY_SEPARATOR . "ConfigTest";
        mkdir(self::$_tmpDir);
    }

    public static function tearDownAfterClass()
    {
        \Magento\Io\File::rmdirRecursive(self::$_tmpDir);
    }

    public function testInstall()
    {
        file_put_contents(self::$_tmpDir . '/local.xml.template', "test; {{date}}; {{base_url}}; {{unknown}}");
        $expectedFile = self::$_tmpDir . '/local.xml';

        $request = $this->getMock(
            'Magento\App\RequestInterface',
            array('getDistroBaseUrl'),
            array(),
            '',
            false
        );

        $request->expects($this->once())->method('getDistroBaseUrl')->will($this->returnValue('http://example.com/'));
        $expectedContents = "test; <![CDATA[d-d-d-d-d]]>; <![CDATA[http://example.com/]]>; {{unknown}}";
        $dirs = new \Magento\App\Dir(
            self::$_tmpDir,
            array(),
            array(\Magento\App\Dir::CONFIG => self::$_tmpDir)
        );

        $this->assertFileNotExists($expectedFile);
        $filesystem = new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local);
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Install\Model\Installer\Config', array(
            'request' => $request, 'dirs' => $dirs, 'filesystem' => $filesystem
        ));
        $model->install();
        $this->assertFileExists($expectedFile);
        $this->assertStringEqualsFile($expectedFile, $expectedContents);
    }

    public function testGetFormData()
    {
        /** @var $model \Magento\Install\Model\Installer\Config */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Install\Model\Installer\Config');
        /** @var $result \Magento\Object */
        $result = $model->getFormData();
        $this->assertInstanceOf('Magento\Object', $result);
        $data = $result->getData();
        $this->assertArrayHasKey('db_host', $data);
    }
}
