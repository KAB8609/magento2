<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pbridge controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_PbridgeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Load only action layout handles
     *
     * @return Enterprise_Pbridge_PbridgeController
     */
    protected function _initActionLayout()
    {
        if (!$this->_checkPbridge()) {
            return;
        }
        $this->_checkPbridge();
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        return $this;
    }

    /**
     * Check is available Payment Bridge module
     *
     * @return bool
     */
    protected function _checkPbridge()
    {
        if (!is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {
            $this->getResponse()->setBody($this->__('Payment Bridge module unavailable.'));
            return false;
        }
        return true;
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return null
     */
    public function indexAction()
    {
        $this->_forward('result');
    }

    /**
     * Result Action
     *
     * @return null
     */
    public function resultAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Output action with params that was given by payment bridge
     *
     * @return viod
     */
    public function outputAction()
    {
        if (!$this->_checkPbridge()) {
            return;
        }
        $this->loadLayout(false);

        $method = $this->getRequest()->getParam('method', false);
        $originalPaymentMethod = $this->getRequest()->getParam('original_payment_method', false);
        $token = $this->getRequest()->getParam('token', false);

        $ccLast4 = $this->getRequest()->getParam('cc_last4', false);
        $ccType  = $this->getRequest()->getParam('cc_type', false);

        if ($originalPaymentMethod && $token && $ccLast4 && $ccType) {
            $message = Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Payment Bridge Selected');
            $methodName = 'payment[pbridge_data][original_payment_method]';
            $inputType = '<input type="hidden"';
            $body = <<<EOT
    <div id="payment_form_{$method}">
        {$message}
        {$inputType} id="{$method}_original_payment_method" name="{$methodName}" value="{$originalPaymentMethod}">
        {$inputType} id="{$method}_token" name="payment[pbridge_data][token]" value="{$token}">
        {$inputType} id="{$method}_cc_last4" name="payment[pbridge_data][cc_last4]" value="{$ccLast4}">
        {$inputType} id="{$method}_cc_type" name="payment[pbridge_data][cc_type]" value="{$ccType}">
    </div>
EOT;
        } else {
            $message = $this->__('Error while reading data from Payment Bridge. Please, try again.');
            $body = <<<EOT
    <div id="payment_form_error">
        {$message}
    </div>
EOT;
        }

        $this->getResponse()->setBody(html_entity_decode(Mage::helper('Mage_XmlConnect_Helper_Data')->htmlize($body)));
    }
}
