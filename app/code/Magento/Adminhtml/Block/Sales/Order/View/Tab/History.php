<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order history tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_View_Tab_History
    extends Magento_Adminhtml_Block_Template
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_template = 'sales/order/view/tab/history.phtml';

    /**
     * Retrieve order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Compose and get order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     *
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = array();
        foreach ($order->getAllStatusHistory() as $orderComment){
            $history[] = $this->_prepareHistoryItem(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $orderComment->getCreatedAtDate(),
                $orderComment->getComment()
            );
        }

        foreach ($order->getCreditmemosCollection() as $_memo){
            $history[] = $this->_prepareHistoryItem(
                __('Credit memo #%1 created', $_memo->getIncrementId()),
                $_memo->getEmailSent(),
                $_memo->getCreatedAtDate()
            );

            foreach ($_memo->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    __('Credit memo #%1 comment added', $_memo->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getShipmentsCollection() as $_shipment){
            $history[] = $this->_prepareHistoryItem(
                __('Shipment #%1 created', $_shipment->getIncrementId()),
                $_shipment->getEmailSent(),
                $_shipment->getCreatedAtDate()
            );

            foreach ($_shipment->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    __('Shipment #%1 comment added', $_shipment->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getInvoiceCollection() as $_invoice){
            $history[] = $this->_prepareHistoryItem(
                __('Invoice #%1 created', $_invoice->getIncrementId()),
                $_invoice->getEmailSent(),
                $_invoice->getCreatedAtDate()
            );

            foreach ($_invoice->getCommentsCollection() as $_comment){
                $history[] = $this->_prepareHistoryItem(
                    __('Invoice #%1 comment added', $_invoice->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $_comment->getCreatedAtDate(),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getTracksCollection() as $_track){
            $history[] = $this->_prepareHistoryItem(
                __('Tracking number %1 for %2 assigned', $_track->getNumber(), $_track->getTitle()),
                false,
                $_track->getCreatedAtDate()
            );
        }

        usort($history, array(__CLASS__, "_sortHistoryByTimestamp"));
        return $history;
    }

    /**
     * Status history date/datetime getter
     *
     * @param array $item
     * @param string $dateType
     * @param string $format
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = 'medium')
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('Magento_Core_Helper_Data')->formatDate($item['created_at'], $format);
        }
        return $this->helper('Magento_Core_Helper_Data')->formatTime($item['created_at'], $format);
    }

    /**
     * Status history item title getter
     *
     * @param array $item
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return (isset($item['title']) ? $this->escapeHtml($item['title']) : '');
    }

    /**
     * Check whether status history comment is with customer notification
     *
     * @param array $item
     * @param boolean $isSimpleCheck
     * @return bool
     */
    public function isItemNotified(array $item, $isSimpleCheck = true)
    {
        if ($isSimpleCheck) {
            return !empty($item['notified']);
        }
        return isset($item['notified']) && false !== $item['notified'];
    }

    /**
     * Status history item comment getter
     *
     * @param array $item
     * @return string
     */
    public function getItemComment(array $item)
    {
        $allowedTags = array('b','br','strong','i','u');
        return (isset($item['comment']) ? $this->escapeHtml($item['comment'], $allowedTags) : '');
    }

    /**
     * Map history items as array
     *
     * @param string $label
     * @param bool $notified
     * @param Zend_Date $created
     * @param string $comment
     * @return array
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return array(
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created
        );
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Comments History');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Order History');
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/commentsHistory', array('_current' => true));
    }

    /**
     * Can Show Tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param array $historyItem
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] == Magento_Sales_Model_Order_Status_History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Comparison For Sorting History By Timestamp
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function _sortHistoryByTimestamp($a, $b)
    {
        $createdAtA = $a['created_at'];
        $createdAtB = $b['created_at'];

        /** @var $createdAta Zend_Date */
        if ($createdAtA->getTimestamp() == $createdAtB->getTimestamp()) {
            return 0;
        }
        return ($createdAtA->getTimestamp() < $createdAtB->getTimestamp()) ? -1 : 1;
    }
}
