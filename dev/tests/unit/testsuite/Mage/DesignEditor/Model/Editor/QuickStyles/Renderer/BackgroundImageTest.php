<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Background image renderer test
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImageTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @cover Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage::toCss
     * @dataProvider backgroundImageData
     */
    public function testToCss($expectedResult, $data)
    {
        $rendererModel = $this->getMock(
            'Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    public function backgroundImageData()
    {
        return array(array(
            'expected_result' => ".header { background-image: url( 'path/image.gif' ); }",
            'data'            => array(
                'type'      => 'image-uploader',
                'default'   => 'bg.gif',
                'selector'  => '.header',
                'attribute' => 'background-image',
                'value'     => 'path/image.gif',
            ),
        ));
    }
}
