<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance history model for adminhtml area
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerBalance\Model\Adminhtml\Balance;

class History extends \Magento\CustomerBalance\Model\Balance\History
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Email\Model\TemplateFactory $templateFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        parent::__construct($templateFactory, $storeManager, $design, $context, $registry, $coreStoreConfig,
            $resource, $resourceCollection, $data);
    }

    /**
     * Add information about admin user who changed customer balance
     *
     * @return \Magento\CustomerBalance\Model\Balance\History
     */
    protected function _beforeSave()
    {
        $balance = $this->getBalanceModel();
        if ((int)$balance->getHistoryAction() == self::ACTION_UPDATED && !$balance->getUpdatedActionAdditionalInfo()) {
            $user = $this->_authSession->getUser();
            if ($user && $user->getUsername()) {
                if (!trim($balance->getComment())) {
                    $this->setAdditionalInfo(__('By admin: %1.', $user->getUsername()));
                } else {
                    $this->setAdditionalInfo(__('By admin: %1. (%2)', $user->getUsername(), $balance->getComment()));
                }
            }
        }

        return parent::_beforeSave();
    }
}