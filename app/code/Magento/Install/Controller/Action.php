<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Install\Controller;

class Action extends \Magento\Core\Controller\Varien\Action
{
    /**
     * @var \Magento\Config\Scope
     */
    protected $_configScope;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var \Magento\View\Design\Theme\ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * Application
     *
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * Application state
     *
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Core\Model\App $app
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Config\Scope $configScope,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Core\Model\App $app,
        \Magento\App\State $appState
    ) {
        $this->_configScope = $configScope;
        $this->_viewDesign = $viewDesign;
        $this->themeProvider = $themeProvider;
        $this->_app = $app;
        $this->_appState = $appState;
        parent::__construct($context);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_configScope->setCurrentScope('install');
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }

    /**
     * Initialize area and design
     *
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = $this->_app->getArea($areaCode);
        $area->load(\Magento\Core\Model\App\Area::PART_CONFIG);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        return $this;
    }

    /**
     * Initialize theme
     *
     * @param string $areaCode
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDefaultTheme($areaCode)
    {
        $this->_viewDesign->setArea($areaCode)
            ->setDesignTheme($this->_viewDesign->getConfigurationDesignTheme($areaCode));
        return $this;
    }
}
