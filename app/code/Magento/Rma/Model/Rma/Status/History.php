<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA model
 */
class Magento_Rma_Model_Rma_Status_History extends Magento_Core_Model_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Rma_Model_RmaFactory
     */
    protected $_rmaFactory;

    /**
     * @var Magento_Rma_Model_Config
     */
    protected $_rmaConfig;

    /**
     * @var Magento_Core_Model_Translate_Proxy
     */
    protected $_translate;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rma_Model_RmaFactory $rmaFactory
     * @param Magento_Rma_Model_Config $rmaConfig
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param Magento_Core_Model_Date $date
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Rma_Model_RmaFactory $rmaFactory,
        Magento_Rma_Model_Config $rmaConfig,
        Magento_Core_Model_Translate $translate,
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        Magento_Core_Model_Date $date,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rmaFactory = $rmaFactory;
        $this->_rmaConfig = $rmaConfig;
        $this->_translate = $translate;
        $this->_templateFactory = $templateFactory;
        $this->_date = $date;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Resource_Rma_Status_History');
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Get RMA object
     *
     * @return Magento_Rma_Model_Rma
     */
    public function getRma()
    {
        if (!$this->hasData('rma') && $this->getRmaEntityId()) {
            /** @var $rma Magento_Rma_Model_Rma */
            $rma = $this->_rmaFactory->create();
            $rma->load($this->getRmaEntityId());
            $this->setData('rma', $rma);
        }
        return $this->getData('rma');
    }

    /**
     * Sending email with comment data
     *
     * @return Magento_Rma_Model_Rma_Status_History
     */
    public function sendCommentEmail()
    {
        $order = $this->getRma()->getOrder();
        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }
        $sendTo = array(
            array(
                'email' => $order->getCustomerEmail(),
                'name'  => $customerName
            )
        );

        return $this->_sendCommentEmail($this->_rmaConfig->getRootCommentEmail(), $sendTo, true);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @return Magento_Rma_Model_Rma_Status_History
     */
    public function sendCustomerCommentEmail()
    {
        $sendTo = array(
            array(
                'email' => $this->_rmaConfig->getCustomerEmailRecipient($this->getStoreId()),
                'name'  => null
            )
        );
        return $this->_sendCommentEmail($this->_rmaConfig->getRootCustomerCommentEmail(), $sendTo, false);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @param string $rootConfig Current config root
     * @param array $sendTo mail recipient array
     * @param bool $isGuestAvailable
     * @return Magento_Rma_Model_Rma_Status_History
     */
    public function _sendCommentEmail($rootConfig, $sendTo, $isGuestAvailable = true)
    {
        $this->_rmaConfig->init($rootConfig, $this->getStoreId());
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $order = $this->getRma()->getOrder();
        $comment = $this->getComment();

        $this->_translate->setTranslateInline(false);
        /** @var $mailTemplate Magento_Core_Model_Email_Template */
        $mailTemplate = $this->_templateFactory->create();
        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();
        if ($copyTo && $copyMethod == 'bcc') {
            foreach ($copyTo as $email) {
                $mailTemplate->addBcc($email);
            }
        }

        if ($isGuestAvailable && $order->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
        } else {
            $template = $this->_rmaConfig->getTemplate();
        }

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'email' => $email,
                    'name'  => null
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $this->getStoreId()
            ))
                ->sendTransactional(
                    $template,
                    $this->_rmaConfig->getIdentity(),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'rma'       => $this->getRma(),
                        'order'     => $this->getRma()->getOrder(),
                        'comment'   => $comment
                    )
                );
        }
        $this->setEmailSent(true);
        $this->_translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Save system comment
     *
     * @return null
     */
    public function saveSystemComment()
    {
        $systemComments = array(
            Magento_Rma_Model_Rma_Source_Status::STATE_PENDING =>
                __('We placed your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED =>
                __('We have authorized your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_PARTIAL_AUTHORIZED =>
                __('We partially authorized your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_RECEIVED =>
                __('We received your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_RECEIVED_ON_ITEM =>
                __('We partially received your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED_ON_ITEM =>
                __('We partially approved your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_REJECTED_ON_ITEM =>
                __('We partially rejected your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED =>
                __('We closed your Return request.'),
            Magento_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED =>
                __('We processed and closed your Return request.'),
        );

        $rma = $this->getRma();
        if (!($rma instanceof Magento_Rma_Model_Rma)) {
            return;
        }

        if (($rma->getStatus() !== $rma->getOrigData('status') && isset($systemComments[$rma->getStatus()]))) {
            $this->setRmaEntityId($rma->getEntityId())
                ->setComment($systemComments[$rma->getStatus()])
                ->setIsVisibleOnFront(true)
                ->setStatus($rma->getStatus())
                ->setCreatedAt($this->_date->gmtDate())
                ->setIsAdmin(1)
                ->save();
        }
    }
}
