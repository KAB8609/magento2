<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/OrderController.php';

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_CatalogController extends Mage_Core_Controller_Front_Action
{
    /**
     * Emulate admin area for certain actions
     */
    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        /**
         * Format actionName => acrResourceId
         */
        $acl = array('notifystock' => 'Mage_Catalog::products', 'review' => 'Mage_Review::reviews_ratings');
        if (isset($acl[$action])) {
            $this->setCurrentArea('adminhtml');
            if (Mage_Rss_OrderController::authenticateAndAuthorizeAdmin($this, $acl[$action])) {
                return;
            }
        }
        parent::preDispatch();
    }

    public function newAction()
    {
        $this->_genericAction('new');
    }

    public function specialAction()
    {
        $this->_genericAction('special');
    }

    public function salesruleAction()
    {
        $this->_genericAction('salesrule');
    }

    public function tagAction()
    {
        if (!$this->_isEnabled('tag')) {
            $this->_forward('nofeed', 'index', 'rss');
            return;
        }
        $tagName = urldecode($this->getRequest()->getParam('tagName'));
        $tagModel = Mage::getModel('Mage_Tag_Model_Tag');
        $tagModel->loadByName($tagName);
        if ($tagModel->getId() && $tagModel->getStatus() == $tagModel->getApprovedStatus()) {
            Mage::register('tag_model', $tagModel);
            $this->_render();
            return;
        }
        $this->_forward('nofeed', 'index', 'rss');
    }

    public function notifystockAction()
    {
        $this->_render();
    }

    public function reviewAction()
    {
        $this->_render();
    }

    public function categoryAction()
    {
         $this->_genericAction('category');
    }

    /**
     * Render or forward to "no route" action if this type of RSS is disabled
     *
     * @param string $code
     */
    protected function _genericAction($code)
    {
        if ($this->_isEnabled($code)) {
            $this->_render();
        } else {
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Whether specified type of RSS is enabled
     *
     * @param string $code
     * @return bool
     */
    protected function _isEnabled($code)
    {
        return Mage::getStoreConfigFlag("rss/catalog/{$code}");
    }

    /**
     * Render as XML-document using layout handle without inheriting any other handles
     */
    protected function _render()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
