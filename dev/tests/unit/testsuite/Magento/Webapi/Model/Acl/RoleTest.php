<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Role|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleService;

    protected function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_roleService = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\Role')
            ->disableOriginalConstructor()
            ->setMethods(array('getIdFieldName', 'getReadConnection', '__wakeup'))
            ->getMock();

        $this->_roleService->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_roleService->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false)));
    }

    /**
     * Create Role model.
     *
     * @param \Magento\Webapi\Model\Resource\Acl\Role $roleService
     * @param \Magento\Webapi\Model\Resource\Acl\Role_Collection $serviceCollection
     * @return \Magento\Webapi\Model\Acl\Role
     */
    protected function _createModel($roleService, $serviceCollection = null)
    {
        return $this->_helper->getObject('Magento\Webapi\Model\Acl\Role', array(
            'eventDispatcher' => $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento\App\CacheInterface', array(), array(), '', false),
            'resource' => $roleService,
            'resourceCollection' => $serviceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_roleService);

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Role', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');
        $entityFactory = $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);

        /** @var \PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Magento\Webapi\Model\Resource\Acl\Role\Collection',
            array('_initSelect', 'setModel'),
            array($entityFactory, $logger, $fetchStrategy, $eventManager, null, $this->_roleService)
        );

        $collection->expects($this->any())->method('setModel')->with('Magento\Webapi\Model\Resource\Acl\Role');

        $model = $this->_createModel($this->_roleService, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Role', '_resourceModel', $result);
    }
}
