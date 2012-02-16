<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 data helper
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Helper_DataTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 data helper
     *
     * @var Mage_Api2_Helper_Data
     */
    protected $_helper;

    /**
     * Rule model mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_helper = Mage::helper('api2');
//        $this->_ruleMock = $this->getModelMockBuilder('api2/resource_acl_filter_attribute')
//            ->setMethods(array('getAllowedAttributes'))
//            ->getMock();
    }

    /**
     * Test get allowed attributes
     */
    public function testGetAllowedAttributes()
    {
        $this->markTestIncomplete("Can't to mock resource model. Investigating...");

        $this->_ruleMock->expects($this->once())
            ->method('getAllowedAttributes')
            ->will($this->returnValue('a,b,c'));

        $this->assertSame(array('a', 'b', 'c'), $this->_helper->getAllowedAttributes(1, 2, 4));
    }

    /**
     * Test get allowed attributes of a rule which has no attributes
     */
    public function testGetAllowedAttributesEmpty()
    {
        $this->markTestIncomplete("Can't to mock resource model. Investigating...");

        $this->_ruleMock->expects($this->once())
            ->method('getAllowedAttributes')
            ->will($this->returnValue(false));

        $this->assertSame(array(), $this->_helper->getAllowedAttributes(1, 2, 4));
    }
}
