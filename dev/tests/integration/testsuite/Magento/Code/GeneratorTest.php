<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/Generator/TestAsset/SourceClassWithNamespace.php';
require_once __DIR__ . '/Generator/TestAsset/ParentClassWithNamespace.php';
/**
 * @magentoAppIsolation enabled
 */
class Magento_Code_GeneratorTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME_WITHOUT_NAMESPACE = 'Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace';
    const CLASS_NAME_WITH_NAMESPACE = 'Magento\Code\Generator\TestAsset\SourceClassWithNamespace';

    /**
     * @var string
     */
    protected $_includePath;

    /**
     * @var Magento_Code_Generator
     */
    protected $_generator;

    /**
     * @var Magento_Code_Generator_Io
     */
    protected $_ioObject;

    protected function setUp()
    {
        $this->_includePath = get_include_path();

        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $generationDirectory = $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/generation';

        Magento_Autoload_IncludePath::addIncludePath($generationDirectory);

        $this->_ioObject = new Magento_Code_Generator_Io(
            new Varien_Io_File(),
            new Magento_Autoload_IncludePath(),
            $generationDirectory
        );
        $this->_generator = Mage::getObjectManager()->create(
            'Magento_Code_Generator', array('ioObject' => $this->_ioObject)
        );
    }

    protected function tearDown()
    {
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $generationDirectory = $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/generation';
        Varien_Io_File::rmdirRecursive($generationDirectory);

        set_include_path($this->_includePath);
        unset($this->_generator);
    }

    protected function _clearDocBlock($classBody)
    {
        return preg_replace('/(\/\*[\w\W]*)\nclass/', 'class', $classBody);
    }

    public function testGenerateClassFactoryWithoutNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITHOUT_NAMESPACE . 'Factory';
        $this->assertEquals(
            Magento_Code_Generator::GENERATION_SUCCESS,
            $this->_generator->generateClass($factoryClassName)
        );

        /** @var $factory Magento_ObjectManager_Factory */
        $factory = Mage::getObjectManager()->create($factoryClassName);
        $object = $factory->create();
        $this->assertInstanceOf(self::CLASS_NAME_WITHOUT_NAMESPACE, $object);

        $content = $this->_clearDocBlock(file_get_contents($this->_ioObject->getResultFileName(
            self::CLASS_NAME_WITHOUT_NAMESPACE . 'Factory')
        ));
        $expectedContent = $this->_clearDocBlock(
            file_get_contents(__DIR__ . '/_files/generatedFactoryWithoutNamespace.php')
        );
        $this->assertEquals($expectedContent, $content);
    }

    public function testGenerateClassFactoryWithNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Factory';
        $this->assertEquals(
            Magento_Code_Generator::GENERATION_SUCCESS,
            $this->_generator->generateClass($factoryClassName)
        );

        /** @var $factory Magento_ObjectManager_Factory */
        $factory = Mage::getObjectManager()->create($factoryClassName);

        $object = $factory->create();
        $this->assertInstanceOf(self::CLASS_NAME_WITH_NAMESPACE, $object);

        $content = $this->_clearDocBlock(
            file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITH_NAMESPACE . 'Factory'))
        );
        $expectedContent = $this->_clearDocBlock(
            file_get_contents(__DIR__ . '/_files/generatedFactoryWithNamespace.php')
        );
        $this->assertEquals($expectedContent, $content);
    }

    public function testGenerateClassProxyWithoutNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITHOUT_NAMESPACE . 'Proxy';
        $this->assertEquals(
            Magento_Code_Generator::GENERATION_SUCCESS,
            $this->_generator->generateClass($factoryClassName)
        );

        $proxy = Mage::getObjectManager()->create($factoryClassName);
        $this->assertInstanceOf(self::CLASS_NAME_WITHOUT_NAMESPACE, $proxy);
        $content = $this->_clearDocBlock(
            file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITHOUT_NAMESPACE . 'Proxy'))
        );
        $expectedContent = $this->_clearDocBlock(
            file_get_contents(__DIR__ . '/_files/generatedProxyWithoutNamespace.php')
        );
        $this->assertEquals($expectedContent, $content);
    }

    public function testGenerateClassProxyWithNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Proxy';
        $this->assertEquals(
            Magento_Code_Generator::GENERATION_SUCCESS,
            $this->_generator->generateClass($factoryClassName)
        );

        $proxy = Mage::getObjectManager()->create($factoryClassName);
        $this->assertInstanceOf(self::CLASS_NAME_WITH_NAMESPACE, $proxy);

        $content = $this->_clearDocBlock(
            file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITH_NAMESPACE . 'Proxy'))
        );
        $expectedContent = $this->_clearDocBlock(
            file_get_contents(__DIR__ . '/_files/generatedProxyWithNamespace.php')
        );
        $this->assertEquals($expectedContent, $content);
    }
}
