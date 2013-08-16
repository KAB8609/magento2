<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_AdminNotification_Model_System_Message_Baseurl
    implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Mage_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Mage_Core_Model_Config_DataFactory
     */
    protected $_configDataFactory;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Model_UrlInterface $urlBuilder
     * @param Mage_Core_Model_Config_DataFactory $configDataFactory
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_UrlInterface $urlBuilder,
        Mage_Core_Model_Config_DataFactory $configDataFactory
    ) {
        $this->_urlBuilder = $urlBuilder;
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
            'default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
        );

        $defaultSecure  = (string) $this->_config->getNode(
            'default/' . Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
        );

        if ($defaultSecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
            || $defaultUnsecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
        ) {
            $output = $this->_urlBuilder->getUrl('adminhtml/system_config/edit', array('section' => 'web'));
        } else {
            /** @var $dataCollection Mage_Core_Model_Resource_Config_Data_Collection */
            $dataCollection = $this->_configDataFactory->create()->getCollection();
            $dataCollection->addValueFilter(Mage_Core_Model_Store::BASE_URL_PLACEHOLDER);

            /** @var $data Mage_Core_Model_Config_Data */
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
        return __('{{base_url}} is not recommended to use in a production environment to declare the Base Unsecure URL / Base Secure URL. It is highly recommended to change this value in your Magento <a href="%1">configuration</a>.', $this->_getConfigUrl());
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
