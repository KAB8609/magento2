<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for entity source model \Magento\ImportExport\Model\Source\Import\Entity
 */
namespace Magento\ImportExport\Model\Source\Import;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var \Magento\ImportExport\Model\Source\Import\Entity
     */
    public static $sourceModel;

    /**
     * Test entity
     *
     * @var array
     */
    protected $_testEntity = array(
        'label' => 'test_label',
        'node'  => 'test_node'
    );

    /**
     * Init source model
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$sourceModel = new \Magento\ImportExport\Model\Source\Import\Entity();
    }

    /**
     * Unregister source model and helper
     *
     * @static
     */
    public static function tearDownAfterClass()
    {
        self::$sourceModel = null;

        $config = new \ReflectionProperty('Mage', '_config');
        $config->setAccessible(true);
        $config->setValue(null, null);
        $config->setAccessible(false);
    }

    /**
     * Mock config
     */
    protected function _mockConfig()
    {
        $configObject = new \Magento\Core\Model\Config\Base(new \Magento\Simplexml\Element('<config></config>'));
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/model_token',
            'Some_Class'
        );
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/label',
            $this->_testEntity['label']
        );

        $config = new \ReflectionProperty('Mage', '_config');
        $config->setAccessible(true);
        $config->setValue(null, $configObject);
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $this->_mockConfig();

        $optionalArray = self::$sourceModel->toOptionArray();

        $this->assertInternalType('array', $optionalArray, 'Result variable must be an array.');
        $this->assertCount(2, $optionalArray);

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }

        $headerElement = $optionalArray[0];
        $dataElement = $optionalArray[1];

        $this->assertEmpty($headerElement['value'], 'Value must be empty.');
        $this->assertEquals($this->_testEntity['node'], $dataElement['value'], 'Incorrect element value.');
        $this->assertEquals($this->_testEntity['label'], $dataElement['label'], 'Incorrect element label.');
    }
}
