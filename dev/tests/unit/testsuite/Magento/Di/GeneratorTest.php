<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Class name parameter value
     */
    const SOURCE_CLASS = 'testClassName';

    /**
     * Expected generated entities
     *
     * @var array
     */
    protected $_expectedEntities = array(
        'factory' => Magento_Di_Generator_Factory::ENTITY_TYPE,
        'proxy' => Magento_Di_Generator_Proxy::ENTITY_TYPE
    );

    /**
     * Model under test
     *
     * @var Magento_Di_Generator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Di_Generator_EntityAbstract
     */
    protected $_generator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Autoload
     */
    protected $_autoloader;

    protected function setUp()
    {
        $this->_generator = $this->getMockForAbstractClass('Magento_Di_Generator_EntityAbstract',
            array(), '', true, true, true,
            array(
                'setSourceClassName',
                'setResultClassName',
                'generate'
            )
        );
        $this->_autoloader = $this->getMock('Magento_Autoload',
            array('classExists'), array(), '', false
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_generator);
        unset($this->_autoloader);

    }

    /**
     * Set generator mock to never call methods
     */
    protected function _prepareGeneratorNeverCalls()
    {
        $this->_generator->expects($this->never())
            ->method('setSourceClassName');
        $this->_generator->expects($this->never())
            ->method('setResultClassName');
        $this->_generator->expects($this->never())
            ->method('generate');
    }

    public function testGetGeneratedEntities()
    {
        $this->_model = new Magento_Di_Generator();
        $this->assertEquals(array_values($this->_expectedEntities), $this->_model->getGeneratedEntities());
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClass($className, $entityType)
    {
        $this->_autoloader->expects($this->once())
            ->method('classExists')
            ->with($className . $entityType)
            ->will($this->returnValue(false));

        $this->_generator->expects($this->once())
            ->method('setSourceClassName')
            ->with($className);
        $this->_generator->expects($this->once())
            ->method('setResultClassName')
            ->with($className . $entityType);
        $this->_generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(true));

        $this->_model = new Magento_Di_Generator($this->_generator, $this->_autoloader);

        $this->assertTrue($this->_model->generateClass($className . $entityType));
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClassWithExistName($className, $entityType)
    {
        $this->_prepareGeneratorNeverCalls();
        $this->_autoloader->expects($this->once())
            ->method('classExists')
            ->with($className . $entityType)
            ->will($this->returnValue(true));

        $this->_model = new Magento_Di_Generator($this->_generator, $this->_autoloader);

        $this->assertFalse($this->_model->generateClass($className . $entityType));
    }

    public function testGenerateClassWithWrongName()
    {
        $this->_prepareGeneratorNeverCalls();
        $this->_autoloader->expects($this->never())
            ->method('classExists');

        $this->_model = new Magento_Di_Generator($this->_generator, $this->_autoloader);

        $this->assertFalse($this->_model->generateClass(self::SOURCE_CLASS));
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testGenerateClassWithError()
    {
        $this->_autoloader->expects($this->once())
            ->method('classExists')
            ->will($this->returnValue(false));

        $this->_generator->expects($this->once())
            ->method('setSourceClassName');
        $this->_generator->expects($this->once())
            ->method('setResultClassName');
        $this->_generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(false));

        $this->_model = new Magento_Di_Generator($this->_generator, $this->_autoloader);

        $expectedEntities = array_values($this->_expectedEntities);
        $resultClassName = self::SOURCE_CLASS . ucfirst(array_shift($expectedEntities));

        $this->_model->generateClass($resultClassName);
    }

    /**
     * Data provider for generate class tests
     *
     * @return array
     */
    public function generateValidClassDataProvider()
    {
        $data = array();
        foreach ($this->_expectedEntities as $generatedEntity) {
            $generatedEntity = ucfirst($generatedEntity);
            $data['test class for ' . $generatedEntity] = array(
                'class name' => self::SOURCE_CLASS,
                'entity type' => $generatedEntity
            );
        }
        return $data;
    }
}
