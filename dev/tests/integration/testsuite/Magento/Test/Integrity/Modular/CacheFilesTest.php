<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class CacheFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @dataProvider cacheConfigDataProvider
     */
    public function testCacheConfig($area)
    {
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        /** @var \Magento\Module\Dir\Reader $moduleReader */
        $moduleReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Module\Dir\Reader');
        $schema = $moduleReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'cache.xsd';

        /** @var \Magento\Core\Model\Cache\Config\Reader $reader */
        $reader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Cache\Config\Reader',
            array(
                'validationState' => $validationStateMock,
                'schema' => $schema,
                'perFileSchema' => $schema,
            )
        );
        try {
            $reader->read($area);
        } catch (\Magento\Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function cacheConfigDataProvider()
    {
        return array(
            'global'    => array('global'),
            'adminhtml' =>array('adminhtml'),
            'frontend'  =>array('frontend'),
        );
    }
}
