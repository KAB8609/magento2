<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Menu\Item;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Item\Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactoryMock;

    /**
     * Constructor params
     *
     * @var array
     */
    protected $_params = array();

    public function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Magento\ObjectManager');
        $this->_helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper', array(), array(), '', false);
        $this->_helperFactoryMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('Magento\Backend\Helper\Data', array(), 'backend_helper'),
                array('Magento\User\Helper\Data', array(), 'user_helper')
            )));

        $this->_model = new \Magento\Backend\Model\Menu\Item\Factory($this->_objectFactoryMock,
            $this->_helperFactoryMock);
    }

    public function testCreate()
    {
        $this->_objectFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Backend\Model\Menu\Item'),
                $this->equalTo(array(
                    'helper' => 'user_helper',
                    'data' => array(
                        'title' => 'item1',
                        'dependsOnModule' => 'Magento\User\Helper\Data',
                    )
                ))
            );
        $this->_model->create(array(
            'module' => 'Magento\User\Helper\Data',
            'title' => 'item1',
            'dependsOnModule' => 'Magento\User\Helper\Data'
        ));
    }

    public function testCreateProvidesDefaultHelper()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Backend\Model\Menu\Item'),
                $this->equalTo(array(
                    'helper' => 'backend_helper',
                    'data' => array()
                ))
        );
        $this->_model->create(array());
    }
}
