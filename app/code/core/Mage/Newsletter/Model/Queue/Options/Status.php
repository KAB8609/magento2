<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter Queue statuses option array
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Newsletter_Model_Queue_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Newsletter Helper Data
     *
     * @var Mage_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Mage_Newsletter_Helper_Data $newsletterHelper)
    {
        $this->_helper = $newsletterHelper;
    }

    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Newsletter_Model_Queue::STATUS_SENT 	=> $this->_helper->__('Sent'),
            Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> $this->_helper->__('Cancelled'),
            Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> $this->_helper->__('Not Sent'),
            Mage_Newsletter_Model_Queue::STATUS_SENDING => $this->_helper->__('Sending'),
            Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> $this->_helper->__('Paused'),
        );
    }
}
