<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml email template model
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Model\Email;

class Template extends \Magento\Email\Model\Template
{
    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    private $_structure;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\App\Emulation $appEmulation
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Email\Model\Template\FilterFactory $emailFilterFactory
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\Dir $dir
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Backend\Model\Config\Structure $structure
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\App\Emulation $appEmulation,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\FileSystem $viewFileSystem,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Email\Model\Template\FilterFactory $emailFilterFactory,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\Dir $dir,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Backend\Model\Config\Structure $structure,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $appEmulation,
            $filesystem,
            $viewUrl,
            $viewFileSystem,
            $design,
            $coreStoreConfig,
            $coreConfig,
            $emailFilterFactory,
            $storeManager,
            $dir,
            $emailConfig,
            $data
        );
        $this->_structure = $structure;
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

        $configData = $this->_coreConfig->getValue(null, 'default');
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

        $templatePaths = $this->_structure
            ->getFieldPathsByAttribute('source_model', 'Magento\Backend\Model\Config\Source\Email\Template');

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
