<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Abstract Controller
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\System;

abstract class ConfigAbstract extends \Magento\Backend\Controller\ActionAbstract
{
    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * Authentication session
     *
     * @var \Magento\Backend\Model\Auth\StorageInterface
     */
    protected $_authSession;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     * @param \Magento\Backend\Model\Auth\StorageInterface $authSession
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure,
        \Magento\Backend\Model\Auth\StorageInterface $authSession
    ) {
        parent::__construct($context);
        $this->_configStructure = $configStructure;
        $this->_authSession = $authSession;
    }

    /**
     * Controller pre-dispatch method
     * Check if current section is found and is allowed
     *
     * @return \Magento\Backend\Controller\ActionAbstract
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $section = null;
        if (!$this->getRequest()->getParam('section')) {
            $section = $this->_configStructure->getFirstSection();
            $this->getRequest()->setParam('section', $section->getId());
        } else {
            $this->_isSectionAllowed($this->getRequest()->getParam('section'));
        }
        return $this;
    }

    /**
     * Check is allow modify system configuration
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::config');
    }

    /**
     * Check if specified section allowed in ACL
     *
     * Will forward to deniedAction(), if not allowed.
     *
     * @param string $sectionId
     * @throws \Exception
     * @return bool
     */
    protected function _isSectionAllowed($sectionId)
    {
        try {
            if (false == $this->_configStructure->getElement($sectionId)->isAllowed()) {
                throw new \Exception('');
            }
            return true;
        } catch (\Zend_Acl_Exception $e) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (\Exception $e) {
            $this->deniedAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
    }

    /**
     * Save state of configuration field sets
     *
     * @param array $configState
     * @return bool
     */
    protected function _saveState($configState = array())
    {
        $adminUser = $this->_authSession->getUser();
        if (is_array($configState)) {
            $extra = $adminUser->getExtra();
            if (!is_array($extra)) {
                $extra = array();
            }
            if (!isset($extra['configState'])) {
                $extra['configState'] = array();
            }
            foreach ($configState as $fieldset => $state) {
                $extra['configState'][$fieldset] = $state;
            }
            $adminUser->saveExtra($extra);
        }
        return true;
    }
}
