<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml email template model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_View_Url $viewUrl
     * @param Mage_Core_Model_View_FileSystem $viewFileSystem
     * @param Mage_Core_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_View_Url $viewUrl,
        Mage_Core_Model_View_FileSystem $viewFileSystem,
        Mage_Core_Model_Config $config,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $filesystem, $viewUrl, $viewFileSystem, $data);
    }

    /**
     * Collect all system config paths where current template is used as default
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedAsDefault()
    {
        $templateCode = $this->getOrigTemplateCode();
        if (!$templateCode) {
            return array();
        }

        $configData = $this->_config->getValue(null, 'default');
        $paths = $this->_findEmailTemplateUsages($templateCode, $configData, '');
        return $paths;
    }

    /**
     * Find nodes which are using $templateCode value
     *
     * @param string $code
     * @param array $data
     * @param string $path
     * @return array
     */
    protected function _findEmailTemplateUsages($code, array $data, $path)
    {
        $output = array();
        foreach ($data as $key => $value) {
            $configPath = $path ? $path . '/' . $key : $key;
            if (is_array($value)) {
                $output = array_merge(
                    $output,
                    $this->_findEmailTemplateUsages($code, $value, $configPath)
                );
            } else {
                if ($value == $code) {
                    $output[] = array('path' => $configPath);
                }
            }
        }
        return $output;
    }


    /**
     * Collect all system config paths where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        $templateId = $this->getId();
        if (!$templateId) {
            return array();
        }

        /** @var Mage_Backend_Model_Config_Structure $configStructure  */
        $configStructure = Mage::getSingleton('Mage_Backend_Model_Config_Structure');
        $templatePaths = $configStructure
            ->getFieldPathsByAttribute('source_model', 'Mage_Backend_Model_Config_Source_Email_Template');

        if (!count($templatePaths)) {
            return array();
        }

        $configData = $this->_getResource()->getSystemConfigByPathsAndTemplateId($templatePaths, $templateId);
        if (!$configData) {
            return array();
        }

        return $configData;
    }
}
