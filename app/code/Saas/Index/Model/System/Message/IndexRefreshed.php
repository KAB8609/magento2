<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_Index_Model_System_Message_IndexRefreshed implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Saas_Index_Model_Flag
     */
    protected $_flag;

    /**
     * Flag state
     *
     * @var int
     */
    protected $_flagState = null;

    /**
     * @var Saas_Index_Helper_Data
     */
    protected $_helper;

    /**
     * @param Saas_Index_Model_FlagFactory $flagFactory
     * @param Saas_Index_Helper_Data $helper
     */
    public function __construct(
        Saas_Index_Model_FlagFactory $flagFactory,
        Saas_Index_Helper_Data $helper
    ) {
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
        $this->_helper = $helper;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return 'INDEX_REFRESH_FINISHED';
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (null === $this->_flagState) {
            $this->_flagState = $this->_flag->getState();
        }

        $isDisplayed = $this->_flagState == Saas_Index_Model_Flag::STATE_FINISHED;

        if ($isDisplayed) {
            $this->_flag->setState(Saas_Index_Model_Flag::STATE_NOTIFIED);
            $this->_flag->save();
        }

        return $isDisplayed;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_helper->__('Search index has been refreshed');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}