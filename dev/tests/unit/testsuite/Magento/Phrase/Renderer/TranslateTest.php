<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Translate_TranslateInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translator;

    /**
     * @var Magento_Phrase_Renderer_Translate
     */
    protected $_renderer;

    public function setUp()
    {
        $this->_translator = $this->getMock('Magento_Translate_TranslateInterface');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject('Magento_Phrase_Renderer_Translate', array(
            'translator' => $this->_translator,
        ));
    }

    public function testTranslate()
    {
        $result = 'rendered text';

        $this->_translator->expects($this->once())->method('translate')
            ->with(array('text', 'param1', 'param2', 'param3'))
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->_renderer->render('text', array('param1', 'param2', 'param3')));
    }
}
