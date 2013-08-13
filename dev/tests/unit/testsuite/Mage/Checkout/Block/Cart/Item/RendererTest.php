<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Cart_Item_RendererTest extends PHPUnit_Framework_TestCase
{
    public function testGetProductThumbnailUrlForConfigurable()
    {
        $url = 'pub/media/catalog/product/cache/1/thumbnail/75x/9df78eab33525d08d6e5fb8d27136e95/_/_/__green.gif';
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $configView = $this->getMock('Magento_Config_View', array('getVarValue'), array(), '', false);
        $configView->expects($this->any())->method('getVarValue')->will($this->returnValue(75));

        $configManager = $this->getMock('Magento_Core_Model_View_Config', array(), array(), '', false);
        $configManager->expects($this->any())->method('getViewConfig')->will($this->returnValue($configView));

        $configurable = $objectManagerHelper->getObject('Mage_Checkout_Block_Cart_Item_Renderer_Configurable',
            array('viewConfig' => $configManager));

        $product = $this->getMock('Magento_Catalog_Model_Product', array('isConfigurable'), array(), '', false);
        $product->expects($this->any())->method('isConfigurable')->will($this->returnValue(true));

        $childProduct =
            $this->getMock('Magento_Catalog_Model_Product', array('getThumbnail', 'getDataByKey'), array(), '', false);
        $childProduct->expects($this->any())->method('getThumbnail')->will($this->returnValue('/_/_/__green.gif'));

        $arguments = array(
            'statusListFactory' => $this->getMock('Mage_Sales_Model_Status_ListFactory', array(), array(), '', false),
        );
        $childItem = $objectManagerHelper->getObject('Mage_Sales_Model_Quote_Item', $arguments);
        $childItem->setData('product', $childProduct);

        $item = $objectManagerHelper->getObject('Mage_Sales_Model_Quote_Item', $arguments);
        $item->setData('product', $product);
        $item->addChild($childItem);

        $helperImage = $this->getMock('Magento_Catalog_Helper_Image',
            array('init', 'resize', '__toString'), array(), '', false
        );
        $helperImage->expects($this->any())->method('init')->will($this->returnValue($helperImage));
        $helperImage->expects($this->any())->method('resize')->will($this->returnValue($helperImage));
        $helperImage->expects($this->any())->method('__toString')->will($this->returnValue($url));

        $layout = $configurable->getLayout();
        $layout->expects($this->any())->method('helper')->will($this->returnValue($helperImage));

        $configurable->setItem($item);

        $configurableUrl = $configurable->getProductThumbnailUrl();
        $this->assertNotNull($configurableUrl);
    }
}
