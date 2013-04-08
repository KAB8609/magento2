<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Translate_InlineParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Translate_InlineParser
     */
    protected $_inlineParser;

    /** @var string */
    protected $_storeId = 'default';

    public static function setUpBeforeClass()
    {
        Mage::getDesign()->setDesignTheme('default/demo');
    }

    public function setUp()
    {
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_CONFIG);
        $this->_inlineParser = Mage::getModel('Mage_Core_Model_Translate_InlineParser');
        /* Called getConfig as workaround for setConfig bug */
        Mage::app()->getStore($this->_storeId)->getConfig('dev/translate_inline/active');
        Mage::app()->getStore($this->_storeId)->setConfig('dev/translate_inline/active', true);
    }

    /**
     * @dataProvider processAjaxPostDataProvider
     */
    public function testProcessAjaxPost($originalText, $translatedText, $isPerStore = null)
    {
        $inputArray = array(array('original' => $originalText, 'custom' => $translatedText));
        if ($isPerStore !== null) {
            $inputArray[0]['perstore'] = $isPerStore;
        }
        /** @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getModel('Mage_Core_Model_Translate_Inline');
        $this->_inlineParser->processAjaxPost($inputArray, $inline);

        $model = Mage::getModel('Mage_Core_Model_Translate_String');
        $model->load($originalText);
        try {
            $this->assertEquals($translatedText, $model->getTranslate());
            $model->delete();
        } catch (Exception $e) {
            Mage::logException($e);
            $model->delete();
        }
    }

    /**
     * @return array
     */
    public function processAjaxPostDataProvider()
    {
        return array(
            array('original text 1', 'translated text 1'),
            array('original text 2', 'translated text 2', true),
        );
    }

    public function testSetGetIsJson()
    {
        $isJsonProperty = new ReflectionProperty(get_class($this->_inlineParser), '_isJson');
        $isJsonProperty->setAccessible(true);

        $this->assertFalse($isJsonProperty->getValue($this->_inlineParser));

        $setIsJsonMethod = new ReflectionMethod($this->_inlineParser, 'setIsJson');
        $setIsJsonMethod->setAccessible(true);
        $setIsJsonMethod->invoke($this->_inlineParser, true);

        $this->assertTrue($isJsonProperty->getValue($this->_inlineParser));
    }
}
