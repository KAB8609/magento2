<?php
/**
 * Test for \Magento\Acl\Loader\Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for \Magento\Acl\Loader\Resource::populateAcl
     */
    public function testPopulateAclOnValidObjects()
    {
        /** @var $aclResource \Magento\Acl\Resource */
        $aclResource = $this->getMock('Magento\Acl\Resource', array(), array(), '', false);

        /** @var $acl \Magento\Acl */
        $acl = $this->getMock('Magento\Acl', array('addResource'), array(), '', false);
        $acl->expects($this->exactly(2))->method('addResource');
        $acl->expects($this->at(0))->method('addResource')->with($aclResource, null)->will($this->returnSelf());
        $acl->expects($this->at(1))->method('addResource')->with($aclResource, $aclResource)->will($this->returnSelf());

        /** @var $factoryObject \Magento\Core\Model\Config */
        $factoryObject = $this->getMock('Magento\Acl\ResourceFactory', array('createResource'), array(), '', false);
        $factoryObject->expects($this->any())->method('createResource')->will($this->returnValue($aclResource));

        /** @var $resourceProvider \Magento\Acl\Resource\ProviderInterface */
        $resourceProvider = $this->getMock('Magento\Acl\Resource\ProviderInterface');
        $resourceProvider->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue(array(
                array(
                    'id' => 'parent_resource::id',
                    'title' => 'Parent Resource Title',
                    'sortOrder' => 10,
                    'children' => array(
                        array(
                            'id' => 'child_resource::id',
                            'title' => 'Child Resource Title',
                            'sortOrder' => 10,
                            'children' => array(),
                        ),
                    ),
                ),
            )));

        /** @var $loaderResource \Magento\Acl\Loader\Resource */
        $loaderResource = new \Magento\Acl\Loader\Resource($resourceProvider, $factoryObject);

        $loaderResource->populateAcl($acl);
    }
}
