<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log admin actions and performed changes.
 * It doesn't log all admin actions, only listed in logging.xml config files.
 */
class Enterprise_Logging_Model_Observer
{

    /**
     * Instance of Enterprise_Logging_Model_Logging
     *
     * @var object Enterprise_Logging_Model_Logging
     */
    protected $_processor;

    /**
     * Initialize Enterprise_Logging_Model_Processor class
     *
     */
    public function __construct()
    {
        $this->_processor = Mage::getSingleton('Enterprise_Logging_Model_Processor');
    }

    /**
     * Mark actions for logging, if required
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPredispatch($observer)
    {
        /* @var $action Mage_Core_Controller_Varien_Action */
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getControllerAction()->getRequest();

        $beforeForwardInfo = $request->getBeforeForwardInfo();

        // Always use current action name bc basing on
        // it we make decision about access granted or denied
        $actionName = $request->getRequestedActionName();

        if (empty($beforeForwardInfo)) {
            $fullActionName = $action->getFullActionName();
        } else {
            $fullActionName = array($request->getRequestedRouteName());

            if (isset($beforeForwardInfo['controller_name'])) {
                $fullActionName[] = $beforeForwardInfo['controller_name'];
            } else {
                $fullActionName[] = $request->getRequestedControllerName();
            }

            if (isset($beforeForwardInfo['action_name'])) {
                $fullActionName[] = $beforeForwardInfo['action_name'];
            } else {
                $fullActionName[] = $actionName;
            }

            $fullActionName = implode('_', $fullActionName);
        }

        $this->_processor->initAction($fullActionName, $actionName);
    }

    /**
     * Model after save observer.
     *
     * @param Varien_Event_Observer
     */
    public function modelSaveAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'save');
    }

    /**
     * Model after delete observer.
     *
     * @param Varien_Event_Observer
     */
    public function modelDeleteAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'delete');
    }

    /**
     * Model after load observer.
     *
     * @param Varien_Event_Observer
     */
    public function modelLoadAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'view');
    }

    /**
     * Log marked actions
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPostdispatch($observer)
    {
        if ($observer->getEvent()->getControllerAction()->getRequest()->isDispatched()) {
            $this->_processor->logAction();
        }
    }

    /**
     * Log successful admin sign in
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionLoginSuccess($observer)
    {
        $this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
    }

    /**
     * Log failure of sign in
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionLoginFailed($observer)
    {
        $eventModel = $this->_logAdminLogin($observer->getUserName());

        if (class_exists('Enterprise_Pci_Model_Observer', false) && $eventModel) {
            $exception = $observer->getException();
            if ($exception->getCode() == Enterprise_Pci_Model_Observer::ADMIN_USER_LOCKED) {
                $eventModel->setInfo(Mage::helper('Enterprise_Logging_Helper_Data')->__('User is locked'))->save();
            }
        }
    }

    /**
     * Log sign in attempt
     *
     * @param string $username
     * @param int $userId
     * @return Enterprise_Logging_Model_Event
     */
    protected function _logAdminLogin($username, $userId = null)
    {
        $eventCode = 'admin_login';
        if (!Mage::getSingleton('Enterprise_Logging_Model_Config')->isActive($eventCode, true)) {
            return;
        }
        $success = (bool)$userId;
        if (!$userId) {
            $userId = Mage::getSingleton('Mage_User_Model_User')->loadByUsername($username)->getId();
        }
        $request = Mage::app()->getRequest();
        return Mage::getSingleton('Enterprise_Logging_Model_Event')->setData(array(
            'ip'         => Mage::helper('Mage_Core_Helper_Http')->getRemoteAddr(),
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => $success,
            'fullaction' => "{$request->getRouteName()}_{$request->getControllerName()}_{$request->getActionName()}",
            'event_code' => $eventCode,
            'action'     => 'login',
        ))->save();
    }

    /**
     * Cron job for logs rotation
     */
    public function rotateLogs()
    {
        $lastRotationFlag = Mage::getModel('Enterprise_Logging_Model_Flag')->loadSelf();
        $lastRotationTime = $lastRotationFlag->getFlagData();
        $rotationFrequency = 3600 * 24 * (int)Mage::getConfig()->getNode('default/system/rotation/frequency');
        if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
            Mage::getResourceModel('Enterprise_Logging_Model_Resource_Event')->rotate(
                3600 * 24 *(int)Mage::getConfig()->getNode('default/system/rotation/lifetime')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}