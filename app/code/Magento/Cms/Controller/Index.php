<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms index controller
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Controller;

class Index extends \Magento\App\Action\Action
{
    /**
     * Renders CMS Home page
     *
     * @param string $coreRoute
     */
    public function indexAction($coreRoute = null)
    {
        $pageId = $this->_objectManager->get('Magento\Core\Model\Store\Config')
            ->getConfig(\Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE);
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('defaultIndex');
        }
    }

    /**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     *
     */
    public function defaultIndexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->_layoutServices->loadLayout();
        $this->renderLayout();
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->_layoutServices->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render Disable cookies page
     *
     */
    public function noCookiesAction()
    {
        $pageId = $this->_objectManager->get('Magento\Core\Model\Store\Config')
            ->getConfig(\Magento\Cms\Helper\Page::XML_PATH_NO_COOKIES_PAGE);
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoCookies');;
        }
    }

    /**
     * Default no cookies page action
     * Used if no cookies page don't configure or available
     *
     */
    public function defaultNoCookiesAction()
    {
        $this->_layoutServices->loadLayout();
        $this->renderLayout();
    }
}
