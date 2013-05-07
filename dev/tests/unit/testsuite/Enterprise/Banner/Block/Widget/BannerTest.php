<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Widget_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Block_Widget_Banner
     */
    private $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerResource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_checkoutSession;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerSession;

    /**
     * @var array
     */
    private $_fixtureBanners = array(456 => '[Banner 456]', 789 => '[Banner 789]');

    protected function setUp()
    {
        $this->_bannerResource = $this->getMock(
            'Enterprise_Banner_Model_Resource_Banner',
            array(
                'filterByTypes',
                'getExistingBannerIdsBySpecifiedIds',
                'getCatalogRuleRelatedBannerIds',
                'getSalesRuleRelatedBannerIds',
                'getBannersContent',
            ),
            array(), '', false
        );

        $this->_checkoutSession = $this->getMock(
            'Mage_Checkout_Model_Session', array('getQuoteId', 'getQuote'), array(), '', false
        );

        $this->_customerSession = $this->getMock(
            'Mage_Customer_Model_Session', array('getCustomerGroupId'), array(), '', false
        );
        $this->_customerSession->expects($this->any())->method('getCustomerGroupId')->will($this->returnValue(4));

        $filter = $this->getMockForAbstractClass('Zend_Filter_Interface');
        $filter->expects($this->any())->method('filter')->will($this->returnArgument(0));
        $cmsHelper = $this->getMock('Mage_Cms_Helper_Data', array('getPageTemplateProcessor'), array(), '', false);
        $cmsHelper->expects($this->any())->method('getPageTemplateProcessor')->will($this->returnValue($filter));

        $currentStore = new Varien_Object(array('id' => 42));
        $currentWebsite = new Varien_Object(array('id' => 57));
        $storeManager = $this->getMockForAbstractClass(
            'Mage_Core_Model_StoreManagerInterface', array(), '', true, true, true, array('getStore', 'getWebsite')
        );
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($currentStore));
        $storeManager->expects($this->once())->method('getWebsite')->will($this->returnValue($currentWebsite));

        $this->_block = new Enterprise_Banner_Block_Widget_Banner(
            $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false),
            $this->_bannerResource,
            $this->getMock('Mage_Core_Model_Session', array(), array(), '', false),
            $this->_checkoutSession,
            $this->_customerSession,
            $cmsHelper,
            $storeManager,
            array(
                'types' => array('footer', 'header'),
                'rotate' => Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_RORATE_NONE,
            )
        );
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_bannerResource = null;
        $this->_checkoutSession = null;
        $this->_customerSession = null;
    }

    public function testGetBannersContentFixed()
    {
        $this->_block->addData(array(
            'banner_ids' => '-123,456,789',
            'display_mode', Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_DISPLAY_FIXED,
        ));

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));
        $this->_bannerResource
            ->expects($this->at(1))
            ->method('getExistingBannerIdsBySpecifiedIds')
            ->with(array(-123, 456, 789))
            ->will($this->returnValue(array(456, 789)))
        ;
        $this->_bannerResource
            ->expects($this->at(2))
            ->method('getBannersContent')
            ->with(array(456, 789), 42)
            ->will($this->returnValue($this->_fixtureBanners))
        ;
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentCatalogRule()
    {
        $this->_block->addData(array(
            'display_mode' => Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_DISPLAY_CATALOGRULE,
        ));

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));
        $this->_bannerResource
            ->expects($this->at(1))
            ->method('getCatalogRuleRelatedBannerIds')
            ->with(57, 4)
            ->will($this->returnValue(array(456, 789)))
        ;
        $this->_bannerResource
            ->expects($this->at(2))
            ->method('getBannersContent')
            ->with(array(456, 789), 42)
            ->will($this->returnValue($this->_fixtureBanners))
        ;
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }

    public function testGetBannersContentSalesRule()
    {
        $this->_block->addData(array(
            'display_mode' => Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_DISPLAY_SALESRULE,
        ));

        $quote = new Varien_Object(array('applied_rule_ids' => '15,11,12'));
        $this->_checkoutSession->expects($this->once())->method('getQuoteId')->will($this->returnValue(8000));
        $this->_checkoutSession->expects($this->once())->method('getQuote')->will($this->returnValue($quote));

        $this->_bannerResource->expects($this->at(0))->method('filterByTypes')->with(array('footer', 'header'));
        $this->_bannerResource
            ->expects($this->at(1))
            ->method('getSalesRuleRelatedBannerIds')
            ->with(array(15, 11, 12))
            ->will($this->returnValue(array(456, 789)))
        ;
        $this->_bannerResource
            ->expects($this->at(2))
            ->method('getBannersContent')
            ->with(array(456, 789), 42)
            ->will($this->returnValue($this->_fixtureBanners))
        ;
        $this->_bannerResource->expects($this->at(3))->method('filterByTypes')->with(array());

        $this->assertEquals($this->_fixtureBanners, $this->_block->getBannersContent());
    }
}
