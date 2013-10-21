<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Cms\Page\Edit\Tab;

/**
 * Test class for \Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design
 * @magentoAppArea adminhtml
 */
class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\View\Design')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('cms_page', $objectManager->create('Magento\Cms\Model\Page'));

        $block = $objectManager->create('Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design');
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('custom_theme_to', 'custom_theme_from') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
