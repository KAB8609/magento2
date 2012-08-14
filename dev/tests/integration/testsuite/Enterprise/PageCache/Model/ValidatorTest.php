<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Integration test for Enterprise_PageCache_Model_Validator
 */
class Enterprise_PageCache_Model_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model under test
     *
     * @var Enterprise_PageCache_Model_Validator
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Enterprise_PageCache_Model_Validator();
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Data provider for testGetDataDependencies
     *
     * @return array
     */
    public function getDataDependenciesDataProvider()
    {
        // test dependency classes are added to config in testGetDataDependencies
        $changeDependency = $this->getMock('stdClass', array(), array(), 'Test_Change_Dependency');
        $deleteDependency = $this->getMock('stdClass', array(), array(), 'Test_Delete_Dependency');

        return array(
            'change_class_for_caching' => array(
                '$type'          => 'change',
                '$object'        => $changeDependency,
                '$isInvalidated' => true,
            ),
            'change_class_not_for_caching' => array(
                '$type'          => 'change',
                '$object'        => new stdClass(),
                '$isInvalidated' => false,
            ),
            'delete_class_for_caching' => array(
                '$type'          => 'delete',
                '$object'        => $deleteDependency,
                '$isInvalidated' => true,
            ),
            'delete_class_not_for_caching' => array(
                '$type'          => 'delete',
                '$object'        => new stdClass(),
                '$isInvalidated' => false,
            ),
        );
    }

    /**
     * Test for both _getDataChangeDependencies and _getDataDeleteDependencies
     *
     * @param string $type
     * @param Varien_Object $object
     * @param boolean $isInvalidated
     *
     * @dataProvider getDataDependenciesDataProvider
     * @covers Enterprise_PageCache_Model_Validator::_getDataChangeDependencies
     * @covers Enterprise_PageCache_Model_Validator::_getDataDeleteDependencies
     *
     * @magentoConfigFixture adminhtml/cache/dependency/change/test Test_Change_Dependency
     * @magentoConfigFixture adminhtml/cache/dependency/delete/test Test_Delete_Dependency
     */
    public function testGetDataDependencies($type, $object, $isInvalidated)
    {
        $cacheType = 'full_page';
        $cacheInstance = Mage::app()->getCacheInstance();
        $cacheInstance->allowUse($cacheType);

        // manual unset cache type
        $cacheInstance->cleanType($cacheType);

        // invoke get data dependencies method
        switch ($type) {
            case 'change':
                $this->_model->checkDataChange($object); // invokes _getDataChangeDependencies
                break;

            case 'delete':
                $this->_model->checkDataDelete($object); // invokes _getDataDeleteDependencies
                break;

            default:
                break;
        }

        // assert cache invalidation status
        $invalidatedTypes = $cacheInstance->getInvalidatedTypes();
        if ($isInvalidated) {
            $this->assertArrayHasKey($cacheType, $invalidatedTypes);
        } else {
            $this->assertArrayNotHasKey($cacheType, $invalidatedTypes);
        }
    }
}
