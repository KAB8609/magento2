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

/**
 * @group module:Mage_Core
 */
class Mage_Core_AjaxControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @dataProvider translateActionDataProvider
     */
    public function testTranslateAction($postData)
    {
        $this->getRequest()->setPost('translate', $postData);
        $this->dispatch('core/ajax/translate');
        $this->assertEquals('{success:true}', $this->getResponse()->getBody());
    }

    public function translateActionDataProvider()
    {
        return array(
            array('test'),
            array(array('test'))
        );
    }
}
