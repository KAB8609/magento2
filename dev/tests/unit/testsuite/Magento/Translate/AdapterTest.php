<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Translate_AdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check that translate calls are passed to given translator
     *
     * @param string $method
     * @param string $strToTranslate
     * @param string $translatedStr
     * @dataProvider translateDataProvider
     */
    public function testTranslate($method, $strToTranslate, $translatedStr)
    {
        $translatorMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('translate'))
            ->getMock();
        $translatorMock->expects($this->once())
            ->method('translate')
            ->with($strToTranslate)
            ->will($this->returnValue($translatedStr));
        $translator = new Magento_Translate_Adapter(array(
            'translator' => array($translatorMock, 'translate')
        ));

        $this->assertEquals($translatedStr, $translator->$method($strToTranslate));
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return array(
            array('translate', 'Translate me!', 'Translated string'),
            array('__', 'Translate me!', 'Translated string')
        );
    }

    /**
     * Test that string is returned in any case
     */
    public function testTranslateNoProxy()
    {
        $translator = new Magento_Translate_Adapter();
        $this->assertEquals('test string', $translator->translate('test string'));
    }
}
