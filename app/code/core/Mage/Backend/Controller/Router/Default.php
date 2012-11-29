<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Controller_Router_Default extends Mage_Core_Controller_Varien_Router_Base
{
    /**
     * List of required request parameters
     * Order sensitive
     * @var array
     */
    protected $_requiredParams = array(
        'areaFrontName',
        'moduleFrontName',
        'controllerName',
        'actionName',
    );

    /**
     * Url key of area
     *
     * @var string
     */
    protected $_areaFrontName;

    /**
     * @param Mage_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_ObjectManager $objectManager
     * @param string $areaCode
     * @param string $baseController
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_ObjectManager $objectManager,
        $areaCode,
        $baseController
    ) {
        parent::__construct($controllerFactory, $objectManager, $areaCode, $baseController);

        $this->_areaFrontName = Mage::helper('Mage_Backend_Helper_Data')->getAreaFrontName();
        if (empty($this->_areaFrontName)) {
            throw new InvalidArgumentException('Area Front Name should be defined');
        }
    }

    /**
     * Fetch default path
     */
    public function fetchDefault()
    {
        $defaultModuleFrontName = (string) Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        // set defaults
        $d = explode('/', $this->_getDefaultPath());
        $this->getFront()->setDefault(array(
            'area'       => !empty($d[0]) ? $d[0] : '',
            'module'     => !empty($d[1]) ? $d[1] : $defaultModuleFrontName,
            'controller' => !empty($d[2]) ? $d[2] : 'index',
            'action'     => !empty($d[3]) ? $d[3] : 'index'
        ));
    }

    /**
     * Get router default request path
     *
     * @return string
     */
    protected function _getDefaultPath()
    {
        return (string)Mage::getConfig()->getNode('default/web/default/admin');
    }

    /**
     * Dummy call to pass through checking
     *
     * @return boolean
     */
    protected function _beforeModuleMatch()
    {
        return true;
    }

    /**
     * checking if we installed or not and doing redirect
     *
     * @return bool
     */
    protected function _afterModuleMatch()
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        return true;
    }

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @return bool
     */
    protected function _noRouteShouldBeApplied()
    {
        return true;
    }

    /**
     * Check whether URL for corresponding path should use https protocol
     *
     * @param string $path
     * @return bool
     */
    protected function _shouldBeSecure($path)
    {
        return substr((string)Mage::getConfig()->getNode('default/web/unsecure/base_url'), 0, 5) === 'https'
            || Mage::getStoreConfigFlag('web/secure/use_in_adminhtml', Mage_Core_Model_App::ADMIN_STORE_ID)
                && substr((string)Mage::getConfig()->getNode('default/web/secure/base_url'), 0, 5) === 'https';
    }

    /**
     * Retrieve current secure url
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return string
     */
    protected function _getCurrentSecureUrl($request)
    {
        return Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->getBaseUrl('link', true) . ltrim($request->getPathInfo(), '/');
    }

    /**
     * Emulate custom admin url
     *
     * @param string $configArea
     * @param bool $useRouterName
     */
    public function collectRoutes($configArea, $useRouterName)
    {
        parent::collectRoutes('admin', $useRouterName);
    }

    /**
     * Check whether redirect should be used for secure routes
     *
     * @return bool
     */
    protected function _shouldRedirectToSecure()
    {
        return false;
    }

    /**
     * Build controller file name based on moduleName and controllerName
     *
     * @param string $realModule
     * @param string $controller
     * @return string
     */
    public function getControllerFileName($realModule, $controller)
    {
        /**
         * Start temporary block
         * TODO: Sprint#27. Delete after adminhtml refactoring
         */
        if ($realModule == 'Mage_Adminhtml') {
            return parent::getControllerFileName($realModule, $controller);
        }
        /**
         * End temporary block
         */

        $parts = explode('_', $realModule);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = Mage::getModuleDir('controllers', $realModule);
        return $file . DS . ucfirst($this->_areaCode) . DS . uc_words($controller, DS) . 'Controller.php';
    }

    /**
     * Build controller class name based on moduleName and controllerName
     *
     * @param string $realModule
     * @param string $controller
     * @return string
     */
    public function getControllerClassName($realModule, $controller)
    {
        /**
         * Start temporary block
         * TODO: Sprint#27. Delete after adminhtml refactoring
         */
        if ($realModule == 'Mage_Adminhtml') {
            return parent::getControllerClassName($realModule, $controller);
        }
        /**
         * End temporary block
         */

        $parts = explode('_', $realModule);
        $realModule = implode('_', array_splice($parts, 0, 2));
        return $realModule . '_' . ucfirst($this->_areaCode) . '_' . uc_words($controller) . 'Controller';
    }

    /**
     * Check whether this router should process given request
     *
     * @param array $params
     * @return bool
     */
    protected function _canProcess(array $params)
    {
        return $params['areaFrontName'] == $this->_areaFrontName;
    }
}
