<?php
/**
 * Mage_Webhook_Model_Subscription_Config
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Subscription_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * alias being used in the _files/config.xml file
     */
    const SUBSCRIPTION_ALIAS = 'subscription_alias';

    /**
     * name being used in the _files/config.xml file
     */
    const SUBSCRIPTION_NAME = 'Test subscriber';

    /**
     * @var Mage_Webhook_Model_Subscription_Config
     */
    private $_config;

    public function setUp()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $dirs = $objectManager->create(
            'Mage_Core_Model_Dir',
            array(
                'baseDir' => BP,
                'dirs'    => array(
                    Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Mage_Core_Model_Dir::CONFIG => __DIR__ . '/_files'
                ),
            )
        );

        $fileResolver = $objectManager->create(
            'Mage_Core_Model_Module_Declaration_FileResolver', array('applicationDirs' => $dirs)
        );
        $filesystemReader = $objectManager->create('Mage_Core_Model_Module_Declaration_Reader_Filesystem',
            array('fileResolver' => $fileResolver)
        );
        $moduleList = $objectManager->create(
            'Mage_Core_Model_ModuleList',
            array('reader' => $filesystemReader, 'cache' => $this->getMock("Magento_Config_CacheInterface"))
        );
        $reader = $objectManager->create(
            'Mage_Core_Model_Config_Modules_Reader', array('dirs' => $dirs, 'moduleList' => $moduleList)
        );
        $modulesLoader = $objectManager->create(
            'Mage_Core_Model_Config_Loader_Modules', array('dirs' => $dirs, 'fileReader' => $reader)
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Mage_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Mage_Core_Model_Config_Storage $storage */
        $storage = $objectManager->create(
            'Mage_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        /** @var Mage_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = $objectManager->create(
            'Mage_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Mage_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = $objectManager->create(
            'Mage_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Mage_Core_Model_Config_Modules_Reader', array(
                'dirs' => $dirs,
                'modulesConfig' => $modulesConfig
            )
        );

        $mageConfig = $objectManager->create(
            'Mage_Core_Model_Config',
            array('storage' => $storage, 'moduleReader' => $moduleReader)
        );

        /** @var Mage_Webhook_Model_Subscription_Config $config */
        $this->_config = $objectManager->create('Mage_Webhook_Model_Subscription_Config',
            array('mageConfig' => $mageConfig)
        );
    }

    public function testReadingConfig()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        /** @var Mage_Webhook_Model_Resource_Subscription_Collection $subscriberCollection */
        $subscriptionSet = $objectManager->create('Mage_Webhook_Model_Resource_Subscription_Collection');

        // Sanity check
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEmpty($subscriptions);
        $this->_config->updateSubscriptionCollection();

        // Test that data matches what we have in config.xml
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEquals(1, count($subscriptions));
        /** @var Mage_Webhook_Model_Subscription $subscription */
        $subscription = array_shift($subscriptions);
        $this->assertEquals(self::SUBSCRIPTION_NAME, $subscription->getName());
    }
}
