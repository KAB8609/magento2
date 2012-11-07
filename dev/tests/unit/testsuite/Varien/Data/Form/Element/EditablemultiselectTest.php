<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Data_Form_Element_EditablemultiselectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form_Element_Editablemultiselect
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Varien_Data_Form_Element_Editablemultiselect();
        $values = array(
            array('value' => 1, 'label' => 'Value1'),
            array('value' => 2, 'label' => 'Value2'),
            array('value' => 3, 'label' => 'Value3'),
        );
        $value = array(1, 3);
        $this->_model->setForm(new Varien_Object());
        $this->_model->setData(array('values' => $values, 'value' => $value));
    }

    public function testGetElementHtmlRendersCustomAttributesWhenDisabled()
    {
        $this->_model->setDisabled(true);
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('disabled="disabled"', $elementHtml);
        $this->assertContains('data-is-removable="no"', $elementHtml);
        $this->assertContains('data-is-editable="no"', $elementHtml);
    }

    public function testGetElementHtmlRendersRelatedJsClassInitialization()
    {
        $this->_model->setElementJsClass('CustomSelect');
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('ElementControl = new CustomSelect(', $elementHtml);
        $this->assertContains('ElementControl.init();', $elementHtml);
    }
}
