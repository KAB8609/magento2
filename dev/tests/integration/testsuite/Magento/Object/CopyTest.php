<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Object;

class CopyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Object\Copy
     */
    protected $_service;

    protected function setUp()
    {
        $this->_service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Object\Copy');
    }

    public function testCopyFieldset()
    {
        $fieldset = 'sales_copy_order';
        $aspect = 'to_edit';
        $data = array(
            'customer_email' => 'admin@example.com',
            'customer_group_id' => '1',
        );
        $source = new \Magento\Object($data);
        $target = new \Magento\Object();
        $expectedTarget = new \Magento\Object($data);
        $expectedTarget->setDataChanges(true); // hack for assertion

        $this->assertNull($this->_service->copyFieldsetToTarget($fieldset, $aspect, 'invalid_source', array()));
        $this->assertNull($this->_service->copyFieldsetToTarget($fieldset, $aspect, array(), 'invalid_target'));
        $this->assertEquals(
            $target,
            $this->_service->copyFieldsetToTarget('invalid_fieldset', $aspect, $source, $target)
        );
        $this->assertSame($target, $this->_service->copyFieldsetToTarget($fieldset, $aspect, $source, $target));
        $this->assertEquals($expectedTarget, $target);
    }

    public function testCopyFieldsetArrayTarget()
    {
        $fieldset = 'sales_copy_order';
        $aspect = 'to_edit';
        $data = array(
            'customer_email' => 'admin@example.com',
            'customer_group_id' => '1',
        );
        $source = new \Magento\Object($data);
        $target = array();
        $expectedTarget = $data;

        $this->assertEquals(
            $target,
            $this->_service->copyFieldsetToTarget('invalid_fieldset', $aspect, $source, $target)
        );
        $this->assertEquals(
            $expectedTarget,
            $this->_service->copyFieldsetToTarget($fieldset, $aspect, $source, $target));
    }
}
