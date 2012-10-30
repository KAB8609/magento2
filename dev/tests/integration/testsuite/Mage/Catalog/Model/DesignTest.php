<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Model_Design.
 */
class Mage_Catalog_Model_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Design
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Design();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testApplyCustomDesign()
    {
        $this->_model->applyCustomDesign('package/theme');
        $this->assertEquals('package', Mage::getDesign()->getPackageName());
        $this->assertEquals('theme', Mage::getDesign()->getTheme());
    }
}
