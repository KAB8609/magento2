<?php
/**
 * Application configuration object. Used to access configuration when application is initialized and installed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class Config implements \Magento\Core\Model\ConfigInterface
{
    /**
     * Config cache tag
     */
    const CACHE_TAG = 'CONFIG';

    /**
     * Default configuration scope
     */
    const SCOPE_DEFAULT = 'default';

    /**
     * Stores configuration scope
     */
    const SCOPE_STORES = 'stores';

    /**
     * Websites configuration scope
     */
    const SCOPE_WEBSITES = 'websites';

    /**
     * @var \Magento\Core\Model\Config\SectionPool
     */
    protected $_sectionPool;

    /**
     * @param Config\SectionPool $sectionPool
     */
    public function __construct(\Magento\Core\Model\Config\SectionPool $sectionPool)
    {
        $this->_sectionPool = $sectionPool;
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = 'default', $scopeCode = null)
    {
        return $this->_sectionPool->getSection($scope, $scopeCode)->getValue($path);
    }

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     */
    public function setValue($path, $value, $scope = 'default', $scopeCode = null)
    {
        $this->_sectionPool->getSection($scope, $scopeCode)->setValue($path, $value);
    }

    /**
     * Reinitialize configuration
     *
     * @return \Magento\Core\Model\Config
     */
    public function reinit()
    {
        $this->_sectionPool->clean();
    }
}
