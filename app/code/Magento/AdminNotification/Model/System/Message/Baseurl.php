<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AdminNotification_Model_System_Message_Baseurl
    implements Magento_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Config_DataFactory
     */
    protected $_configDataFactory;

    /**
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_Config_DataFactory $configDataFactory
     */
    public function __construct(
        Magento_Core_Model_Config $config,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_Config_DataFactory $configDataFactory
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_helperFactory = $helperFactory;
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_configDataFactory = $configDataFactory;
    }

    /**
     * Get url for config settings where base url option can be changed
     *
     * @return string
     */
    protected function _getConfigUrl()
    {
        $output = '';
        $defaultUnsecure= (string) $this->_config->getNode(
            'default/' . Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
        );

        $defaultSecure  = (string) $this->_config->getNode(
            'default/' . Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL
        );

        if ($defaultSecure == Magento_Core_Model_Store::BASE_URL_PLACEHOLDER
            || $defaultUnsecure == Magento_Core_Model_Store::BASE_URL_PLACEHOLDER
        ) {
            $output = $this->_urlBuilder->getUrl('adminhtml/system_config/edit', array('section' => 'web'));
        } else {
            /** @var $dataCollection Magento_Core_Model_Resource_Config_Data_Collection */
            $dataCollection = $this->_configDataFactory->create()->getCollection();
            $dataCollection->addValueFilter(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER);

            /** @var $data Magento_Core_Model_Config_Data */
            foreach ($dataCollection as $data) {
                if ($data->getScope() == 'stores') {
                    $code = $this->_storeManager->getStore($data->getScopeId())->getCode();
                    $output = $this->_urlBuilder->getUrl(
                        'adminhtml/system_config/edit', array('section' => 'web', 'store' => $code)
                    );
                    break;
                } elseif ($data->getScope() == 'websites') {
                    $code = $this->_storeManager->getWebsite($data->getScopeId())->getCode();
                    $output = $this->_urlBuilder->getUrl(
                        'adminhtml/system_config/edit', array('section' => 'web', 'website' => $code)
                    );
                    break;
                }
            }
        }
        return $output;
    }


    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('BASE_URL' . $this->_getConfigUrl());
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return (bool) $this->_getConfigUrl();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_helperFactory->get('Magento_AdminNotification_Helper_Data')->__('{{base_url}} is not recommended to use in a production environment to declare the Base Unsecure URL / Base Secure URL. It is highly recommended to change this value in your Magento <a href="%s">configuration</a>.', $this->_getConfigUrl());
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
