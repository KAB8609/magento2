<?php
namespace Magento\Widget\Model\Config;
/**
 * \Magento\Widget\Model\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\App\Dir $dirs */
        $dirs = $objectManager->create(
            'Magento\App\Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\App\Dir::MODULES => __DIR__ . '/_files/code',
                    \Magento\App\Dir::CONFIG => __DIR__ . '/_files/code'
                )
            )
        );

        /** @var \Magento\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento\Module\Declaration\FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var \Magento\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento\Module\Declaration\Reader\Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var \Magento\Module\ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento\Module\ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var \Magento\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Module\Dir\Reader', array(
                'moduleList' => $modulesList
            )
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');

        /** @var \Magento\Widget\Model\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Widget\Model\Config\FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $this->_model = $objectManager->create(
            'Magento\Widget\Model\Config\Reader', array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema,
            )
        );
    }

    public function testRead()
    {
        $result = $this->_model->read('global');
        $expected = include '_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testReadFile()
    {
        $result = $this->_model->readFile(__DIR__ . '/_files/code/Magento/Test/etc/widget.xml');
        $expected = include '_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/widgetFirst.xml',
            __DIR__ . '/_files/widgetSecond.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('widget.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';

        /** @var \Magento\Widget\Model\Config\Reader $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Widget\Model\Config\Reader', array(
                'fileResolver' => $fileResolverMock,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema
            )
        );
        $output = $model->read('global');
        $expected = include '_files/expectedMergedArray.php';
        $this->assertEquals($expected, $output);
    }
}
