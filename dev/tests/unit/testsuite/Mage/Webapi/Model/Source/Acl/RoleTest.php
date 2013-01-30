<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Model_Source_Acl_Role.
 */
class Mage_Webapi_Model_Source_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check output format.
     *
     * @dataProvider toOptionsHashDataProvider
     *
     * @param bool $addEmpty
     * @param array $data
     * @param array $expected
     */
    public function testToOptionHashFormat($addEmpty, $data, $expected)
    {
        $resourceMock = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_Role')
            ->setMethods(array('getRolesList'))
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->any())
            ->method('getRolesList')
            ->will($this->returnValue($data));

        $model = new Mage_Webapi_Model_Source_Acl_Role(array(
            'resource' => $resourceMock
        ));

        $options = $model->toOptionHash($addEmpty);
        $this->assertEquals($expected, $options);
    }

    /**
     * Data provider for testing toOptionHash.
     *
     * @return array
     */
    public function toOptionsHashDataProvider()
    {
        return array(
            'with empty' => array(
                true, array('1' => 'role 1', '2' => 'role 2'), array('' => '', '1' => 'role 1', '2' => 'role 2')
            ),
            'without empty' => array(
                false, array('1' => 'role 1', '2' => 'role 2'), array('1' => 'role 1', '2' => 'role 2')
            ),
        );
    }
}
