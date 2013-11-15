<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model responsible for configuration of minified asset
 */
namespace Magento\Core\Model\Page\Asset;

class MinifyService
{
    /**#@+
     * XPaths to minification configuration
     */
    const XML_PATH_MINIFICATION_ENABLED = 'dev/%s/minify_files';
    const XML_PATH_MINIFICATION_ADAPTER = 'dev/%s/minify_adapter';
    /**#@-*/

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_enabled = array();

    /**
     * @var \Magento\Code\Minifier[]
     */
    protected $_minifiers = array();

    /**
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\Store\Config $config
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\Dir $dirs
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $config,
        \Magento\ObjectManager $objectManager,
        \Magento\App\Dir $dirs,
        \Magento\App\State $appState
    ) {
        $this->_storeConfig = $config;
        $this->_objectManager = $objectManager;
        $this->_dirs = $dirs;
        $this->_appState = $appState;
    }

    /**
     * Get filtered assets
     * Assets applicable for minification are wrapped with the minified asset
     *
     * @param array|Iterator $assets
     * @return array
     */
    public function getAssets($assets)
    {
        $resultAssets = array();
        /** @var $asset \Magento\Core\Model\Page\Asset\AssetInterface */
        foreach ($assets as $asset) {
            $contentType = $asset->getContentType();
            if ($this->_isEnabled($contentType)) {
                $asset = $this->_objectManager
                    ->create('Magento\Core\Model\Page\Asset\Minified', array(
                        'asset' => $asset,
                        'minifier' => $this->_getMinifier($contentType)
                    ));
            }
            $resultAssets[] = $asset;
        }
        return $resultAssets;
    }

    /**
     * Get minifier object configured with specified content type
     *
     * @param string $contentType
     * @return \Magento\Code\Minifier
     */
    protected function _getMinifier($contentType)
    {
        if (!isset($this->_minifiers[$contentType])) {
            $adapter = $this->_getAdapter($contentType);
            $strategyParams = array(
                'adapter' => $adapter,
            );
            switch ($this->_appState->getMode()) {
                case \Magento\App\State::MODE_PRODUCTION:
                    $strategy = $this->_objectManager->create('Magento\Code\Minifier\Strategy\Lite', $strategyParams);
                    break;
                default:
                    $strategy = $this->_objectManager
                        ->create('Magento\Code\Minifier\Strategy\Generate', $strategyParams);
            }

            $this->_minifiers[$contentType] = $this->_objectManager->create('Magento\Code\Minifier',
                array(
                    'strategy' => $strategy,
                    'directoryName' =>  'minify',
                )
            );
        }
        return $this->_minifiers[$contentType];
    }

    /**
     * Check if minification is enabled for specified content type
     *
     * @param $contentType
     * @return bool
     */
    protected function _isEnabled($contentType)
    {
        if (!isset($this->_enabled[$contentType])) {
            $this->_enabled[$contentType] = $this->_storeConfig->getConfigFlag(
                sprintf(self::XML_PATH_MINIFICATION_ENABLED, $contentType)
            );
        }
        return $this->_enabled[$contentType];
    }

    /**
     * Get minification adapter by specified content type
     *
     * @param $contentType
     * @return mixed
     * @throws \Magento\Core\Exception
     */
    protected function _getAdapter($contentType)
    {
        $adapterClass = $this->_storeConfig->getConfig(
            sprintf(self::XML_PATH_MINIFICATION_ADAPTER, $contentType)
        );
        if (!$adapterClass) {
            throw new \Magento\Core\Exception(
                "Minification adapter is not specified for '$contentType' content type"
            );
        }

        $adapter = $this->_objectManager->create($adapterClass);
        return $adapter;
    }
}
