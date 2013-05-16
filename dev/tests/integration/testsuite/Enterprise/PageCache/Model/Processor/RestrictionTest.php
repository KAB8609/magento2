<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_Processor_RestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        Mage::app()->getCacheInstance()->allowUse('full_page');
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('Enterprise_PageCache_Model_Processor');
    }

    public function testIsAllowedNoCacheCookie()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_COOKIE[Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedNoCacheGetParam()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_GET['no_cache'] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }
}
