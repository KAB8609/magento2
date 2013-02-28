<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test formatted time data
     */
    const TEST_FORMATTED_TIME = 'test_time';

    public function testBeforeSave()
    {
        $resourceModel = $this->getMock(
            'Mage_Core_Model_Resource_Layout_Update',
            array('formatDate', 'getIdFieldName', 'beginTransaction', 'save', 'addCommitCallback', 'commit'),
            array(),
            '',
            false
        );
        $resourceModel->expects($this->once())
            ->method('formatDate')
            ->with($this->isType('int'))
            ->will($this->returnValue(self::TEST_FORMATTED_TIME));
        $resourceModel->expects($this->once())
            ->method('addCommitCallback')
            ->will($this->returnSelf());

        $helper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $model Mage_Core_Model_Layout_Update */
        $model = $helper->getObject('Mage_Core_Model_Layout_Update', array('resource' => $resourceModel));
        $model->setId(0); // set any data to set _hasDataChanges flag
        $model->save();

        $this->assertEquals(self::TEST_FORMATTED_TIME, $model->getUpdatedAt());
    }
}
