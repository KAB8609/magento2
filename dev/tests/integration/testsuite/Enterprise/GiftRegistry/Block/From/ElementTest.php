<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_GiftRegistry_Block_Form_Element
 */
class Enterprise_GiftRegistry_Block_Form_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testGetCalendarDateHtml()
    {
        $block = new Enterprise_GiftRegistry_Block_Form_Element;
        $block->setLayout(new Mage_Core_Model_Layout);

        $value = null;
        $formatType = Mage_Core_Model_Locale::FORMAT_TYPE_FULL;

        $html = $block->getCalendarDateHtml('date_name', 'date_id', $value, $formatType);

        $dateFormat = Mage::app()->getLocale()->getDateFormat($formatType);

        $this->assertContains('dateFormat: "' . $dateFormat . '",', $html);
        $this->assertContains('value=""', $html);
    }
}
