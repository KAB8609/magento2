<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Widget;

class InstanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Widget\Instance
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget\Instance');
    }

    public function testSetGetType()
    {
        $this->assertEmpty($this->_model->getType());
        $this->assertSame('test', $this->_model->setType('test')->getType());
        $this->assertSame('test', $this->_model->getInstanceType());
    }

    public function testSetThemeId()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->setAreaCode('frontend');
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme()
            ->getDesignTheme();
        $this->_model->setThemeId($theme->getId());

        $this->assertEquals($theme->getId(), $this->_model->getThemeId());
    }

    /**
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function testGetWidgetConfigAsArray()
    {
        $this->markTestIncomplete('Functionality is failed because widget'
            . ' "app/design/frontend/magento_iphone_html5/etc/widget.xml" replaces'
            . ' "new_products" widget in Catalog module');
        $config = $this->_model->setType('Magento\Catalog\Block\Product\Widget\NewWidget')->getWidgetConfigAsArray();
        $this->assertTrue(is_array($config));
        $element = null;
        if (isset($config['parameters']) && isset($config['parameters']['template'])
            && isset($config['parameters']['template']['values'])
            && isset($config['parameters']['template']['values']['list'])
        ) {
            $element = $config['parameters']['template']['values']['list'];
        }
        $expected = array(
            'value' => 'product/widget/new/content/new_list.phtml',
            'label' => 'New Products List Template'
        );
        $this->assertNotNull($element);
        $this->assertEquals($expected, $element);

        return $this->_model;
    }

    /**
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function testGetWidgetSupportedContainers()
    {
        $this->markTestIncomplete('Functionality is failed because widget'
            . ' "app/design/frontend/magento_iphone_html5/etc/widget.xml" replaces'
            . ' "new_products" widget in Catalog module');
        $this->_model->setType('Magento\Catalog\Block\Product\Widget\NewWidget');
        $containers = $this->_model->getWidgetSupportedContainers();
        $this->assertInternalType('array', $containers);
        $this->assertContains('left', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('right', $containers);
        return $this->_model;
    }

    /**
     * @param \Magento\Widget\Model\Widget\Instance $model
     * @depends testGetWidgetSupportedContainers
     */
    public function testGetWidgetSupportedTemplatesByContainer($model)
    {
        $templates = $model->getWidgetSupportedTemplatesByContainer('content');
        $this->assertNotEmpty($templates);
        $this->assertInternalType('array', $templates);
        foreach ($templates as $row) {
            $this->assertArrayHasKey('value', $row);
            $this->assertArrayHasKey('label', $row);
        }
    }

    /**
     * @param \Magento\Widget\Model\Widget\Instance $model
     * @depends testGetWidgetConfigAsArray
     */
    public function testGenerateLayoutUpdateXml(\Magento\Widget\Model\Widget\Instance $model)
    {
        $params = array(
            'display_mode' => 'fixed',
            'types'        => array('type_1', 'type_2'),
        );
        $model->setData('widget_parameters', $params);
        $this->assertEquals('', $model->generateLayoutUpdateXml('content'));
        $model->setId('test_id')->setPackageTheme('magento_plushe');
        $result = $model->generateLayoutUpdateXml('content');
        $this->assertContains('<referenceContainer name="content">', $result);
        $this->assertContains('<block class="' . $model->getType() . '"', $result);
        $this->assertEquals(count($params), substr_count($result, '<action method="setData">'));
        $this->assertContains('<argument name="name" xsi:type="string">display_mode</argument>', $result);
        $this->assertContains('<argument name="value" xsi:type="string">fixed</argument>', $result);
        $this->assertContains('<argument name="name" xsi:type="string">types</argument>', $result);
        $this->assertContains('<argument name="value" xsi:type="string">type_1,type_2</argument>', $result);
    }
}
