<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Db_Collection_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_model = null;

    protected function setUp()
    {
        $resource = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract',
            array(), '', true, true, true, array('getMainTable', 'getIdFieldName')
        );

        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue($resource->getTable('core_website')));
        $resource->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('website_id'));

        $this->_model = $this->getMockForAbstractClass(
            'Mage_Core_Model_Resource_Db_Collection_Abstract',
            array($resource)
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testGetAllIds()
    {
        $allIds = $this->_model->getAllIds();
        sort($allIds);
        $this->assertEquals(array('0', '1'), $allIds);
    }

    public function testGetAllIdsWithBind()
    {
        $this->_model->getSelect()->where('code = :code');
        $this->_model->addBindParam('code', 'admin');
        $this->assertEquals(array('0'), $this->_model->getAllIds());
    }
}
