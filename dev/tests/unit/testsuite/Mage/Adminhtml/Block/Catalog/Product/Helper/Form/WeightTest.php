<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_WeightTest extends PHPUnit_Framework_TestCase
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight
     */
    protected $_model;

    /**
     * @var Magento_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    public function testSetForm()
    {
        $this->_virtual = new Magento_Object();

        $helper = $this->getMock('Mage_Catalog_Helper_Product', array('getTypeSwitcherControlLabel'),
            array(), '', false, false
        );
        $helper->expects($this->any())->method('getTypeSwitcherControlLabel')
            ->will($this->returnValue('Virtual / Downloadable'));

        $this->assertNull($this->_virtual->getId());
        $this->assertNull($this->_virtual->getName());
        $this->assertNull($this->_virtual->getLabel());
        $this->assertNull($this->_virtual->getForm());

        $this->_model = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight(
            array('element' => $this->_virtual, 'helper' => $helper)
        );

        $form = new Magento_Data_Form();
        $this->_model->setForm($form);

        $this->assertEquals(
            Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight::VIRTUAL_FIELD_HTML_ID,
            $this->_virtual->getId()
        );
        $this->assertEquals('is_virtual', $this->_virtual->getName());
        $this->assertEquals('Virtual / Downloadable', $this->_virtual->getLabel());
        $this->assertSame($form, $this->_virtual->getForm());
    }
}
