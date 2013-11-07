<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Modular;

class SystemConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = $objectManager->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled(\Magento\Core\Model\Cache\Type\Config::TYPE_IDENTIFIER, false);

        /** @var $dirs \Magento\App\Dir */
        $dirs = $objectManager->get('Magento\App\Dir');
        $modulesDir = $dirs->getDir(\Magento\App\Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Magento\Module\Dir\Reader', array('getConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue($fileList))
        ;
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Backend')
            ->will($this->returnValue($modulesDir . '/Magento/Backend/etc'))
        ;
        try {
            $objectManager->create('Magento\Backend\Model\Config\Structure\Reader', array(
                'moduleReader' => $configMock,
                'runtimeValidation' => true,
            ));
        } catch (\Magento\Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
