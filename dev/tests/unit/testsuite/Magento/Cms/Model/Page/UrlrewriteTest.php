<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Cms_Model_Page_UrlrewriteTest
 */
class Magento_Cms_Model_Page_UrlrewriteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cms_Model_Page_Urlrewrite
     */
    protected $_model = null;

    /**
     * @var Magento_Object
     */
    protected $_cmsPage = null;

    /**
     * Prepare test classes
     */
    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento_Cms_Model_Page_Urlrewrite')
            ->setMethods(array('getResourceModelInstance'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cmsPage = new Magento_Object(array(
            'id' => 3,
            'identifier' => 'cms-page'
        ));
    }

    /**
     * Clear created objects
     */
    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_cmsPage);
    }

    /**
     * Test generateIdPath
     */
    public function testGenerateIdPath()
    {
        $this->assertEquals('cms_page/3', $this->_model->generateIdPath($this->_cmsPage));
    }

    /**
     * Test generateTargetPath
     */
    public function testGenerateTargetPath()
    {
        $this->assertEquals('cms/page/view/page_id/3', $this->_model->generateTargetPath($this->_cmsPage));
    }

    /**
     * Test generateRequestPath
     */
    public function testGenerateRequestPath()
    {
        $this->assertEquals('cms-page', $this->_model->generateRequestPath($this->_cmsPage));
    }
}