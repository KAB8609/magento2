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

class Mage_Core_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page
     */
    protected $_object;

    /**
     * @var
     */
    protected $_pageAssets;

    protected function setUp()
    {
        $this->_pageAssets = new Mage_Core_Model_Page_Asset_Collection;
        $this->_object = new Mage_Core_Model_Page($this->_pageAssets);
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_object = null;
    }

    public function testGetAssets()
    {
        $this->assertSame($this->_pageAssets, $this->_object->getAssets());
    }
}
