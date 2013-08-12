<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_SalesArchive
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_SalesArchive_Model_Order_Archive_Grid_Row_UrlGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Enterprise_SalesArchive_Model_Order_Archive_Grid_Row_UrlGenerator
     */
    protected $_model;

    /**
     * @var $_authorization PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var $_urlModel PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    protected function setUp()
    {
        $this->_authorizationMock = $this->getMockBuilder('Magento_AuthorizationInterface')
            ->getMock();

        $this->_urlModelMock = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $urlMap = array(
            array(
                '*/sales_order/view',
                array(
                    'order_id' => null
                ),
                'http://localhost/backend/admin/sales_order/view/order_id/'
            ),
            array(
                '*/sales_order/view',
                array(
                    'order_id' => 1
                ),
                'http://localhost/backend/admin/sales_order/view/order_id/1'
            ),
        );
        $this->_urlModelMock->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap($urlMap));

        $this->_model = new Enterprise_SalesArchive_Model_Order_Archive_Grid_Row_UrlGenerator(
            $this->_authorizationMock,
            array(
                'path' => '*/sales_order/view',
                'urlModel' => $this->_urlModelMock,
                'extraParamsTemplate' => array(
                    'order_id' => 'getId'
                )
            )
        );
    }

    public function testAuthNotAllowed()
    {
        $this->_authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Enterprise_SalesArchive::orders')
            ->will($this->returnValue(false));

        $this->assertFalse($this->_model->getUrl(new Magento_Object()));
    }

    /**
     * @param $item
     * @param $expectedUrl
     * @dataProvider itemsDataProvider
     */
    public function testAuthAllowed($item, $expectedUrl)
    {
        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Enterprise_SalesArchive::orders')
            ->will($this->returnValue(true));

        $this->assertEquals($expectedUrl, $this->_model->getUrl($item));
    }

    public function itemsDataProvider()
    {
        return array(
            array(
                new Magento_Object(),
                'http://localhost/backend/admin/sales_order/view/order_id/'
            ),
            array(
                new Magento_Object(
                    array(
                        'id' => 1
                    )
                ),
                'http://localhost/backend/admin/sales_order/view/order_id/1'
            )
        );
    }
}
