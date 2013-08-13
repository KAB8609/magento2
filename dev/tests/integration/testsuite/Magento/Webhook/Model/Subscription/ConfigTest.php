<?php
/**
 * Magento_Webhook_Model_Subscription_Config
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_ConfigTest extends PHPUnit_Framework_TestCase
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
     * @var Magento_Webhook_Model_Subscription_Config
     */
    private $_config;

    public function setUp()
    {

        $dirs = Mage::getObjectManager()->create(
            'Magento_Core_Model_Dir',
            array(
                'baseDir' => array(BP),
                'dirs'    => array(Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files'),
            )
        );
        $modulesLoader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules',
            array('dirs' => $dirs)
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Magento_Core_Model_Config_Storage $storage */
        $storage = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        $config = new Magento_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /** @var Magento_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Magento_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'fileReader' => $fileReader,
                'modulesConfig' => $modulesConfig
            )
        );

        $mageConfig = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config',
            array('storage' => $storage, 'moduleReader' => $moduleReader)
        );

        /** @var Magento_Webhook_Model_Subscription_Config $config */
        $this->_config = Mage::getObjectManager()->create('Magento_Webhook_Model_Subscription_Config',
            array('mageConfig' => $mageConfig)
        );
    }

    public function testReadingConfig()
    {

        /** @var Magento_Webhook_Model_Resource_Subscription_Collection $subscriberCollection */
        $subscriptionSet = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Subscription_Collection');

        // Sanity check
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEmpty($subscriptions);
        $this->_config->updateSubscriptionCollection();

        // Test that data matches what we have in config.xml
        $subscriptions = $subscriptionSet->getSubscriptionsByAlias(self::SUBSCRIPTION_ALIAS);
        $this->assertEquals(1, count($subscriptions));
        /** @var Magento_Webhook_Model_Subscription $subscription */
        $subscription = array_shift($subscriptions);
        $this->assertEquals(self::SUBSCRIPTION_NAME, $subscription->getName());
    }
}