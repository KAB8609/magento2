<?php
/**
 * Magento_Webhook_Model_Source_Hook
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Source_HookTest extends Magento_Webhook_Model_Source_Pkg
{
    public function testGetTopicsForForm()
    {
        $unitUnderTest = new Magento_Webhook_Model_Source_Hook($this->_mockTranslate, $this->_mockConfig);
        $elements = $unitUnderTest->getTopicsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getTopicsForForm();
        $this->assertSame($elements, $secondResult);
    }
}
