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

namespace Magento\Catalog\Model\Product\Type\Configurable;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Type\Configurable\Attribute
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product\Type\Configurable\Attribute');
    }

    public function testAddPrice()
    {
        $this->assertEmpty($this->_model->getPrices());
        $this->_model->addPrice(100);
        $this->assertEquals(array(100), $this->_model->getPrices());
    }

    public function testGetLabel()
    {
        $this->assertEmpty($this->_model->getLabel());
        $this->_model->setProductAttribute(new \Magento\Object(array('store_label' => 'Store Label')));
        $this->assertEquals('Store Label', $this->_model->getLabel());

        $this->_model->setUseDefault(1)
            ->setProductAttribute(new \Magento\Object(array('store_label' => 'Other Label')));
        $this->assertEquals('Other Label', $this->_model->getLabel());
    }
}
