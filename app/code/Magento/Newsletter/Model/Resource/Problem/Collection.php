<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter problems collection
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Resource_Problem_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * True when subscribers info joined
     *
     * @var bool
     */
    protected $_subscribersInfoJoinedFlag  = false;

    /**
     * True when grouped
     *
     * @var bool
     */
    protected $_problemGrouped             = false;

    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Newsletter_Model_Problem', 'Magento_Newsletter_Model_Resource_Problem');
    }

    /**
     * Adds subscribers info
     *
     * @return Magento_Newsletter_Model_Resource_Problem_Collection
     */
    public function addSubscriberInfo()
    {
        $this->getSelect()->joinLeft(array('subscriber'=>$this->getTable('newsletter_subscriber')),
            'main_table.subscriber_id = subscriber.subscriber_id',
            array('subscriber_email','customer_id','subscriber_status')
        );
        $this->addFilterToMap('subscriber_id', 'main_table.subscriber_id');
        $this->_subscribersInfoJoinedFlag = true;

        return $this;
    }

    /**
     * Adds queue info
     *
     * @return Magento_Newsletter_Model_Resource_Problem_Collection
     */
    public function addQueueInfo()
    {
        $this->getSelect()->joinLeft(array('queue'=>$this->getTable('newsletter_queue')),
            'main_table.queue_id = queue.queue_id',
            array('queue_start_at', 'queue_finish_at')
        )
        ->joinLeft(array('template'=>$this->getTable('newsletter_template')), 'queue.template_id = template.template_id',
            array('template_subject','template_code','template_sender_name','template_sender_email')
        );
        return $this;
    }

    /**
     * Loads customers info to collection
     *
     */
    protected function _addCustomersData()
    {
        $customersIds = array();

        foreach ($this->getItems() as $item) {
            if ($item->getCustomerId()) {
                $customersIds[] = $item->getCustomerId();
            }
        }

        if (count($customersIds) == 0) {
            return;
        }

        $customers = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
            ->addNameToSelect()
            ->addAttributeToFilter('entity_id', array("in"=>$customersIds));

        $customers->load();

        foreach ($customers->getItems() as $customer) {
            $problems = $this->getItemsByColumnValue('customer_id', $customer->getId());
            foreach ($problems as $problem) {
                $problem->setCustomerName($customer->getName())
                    ->setCustomerFirstName($customer->getFirstName())
                    ->setCustomerLastName($customer->getLastName());
            }
        }
    }

    /**
     * Loads collecion and adds customers info
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Magento_Newsletter_Model_Resource_Problem_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        if ($this->_subscribersInfoJoinedFlag && !$this->isLoaded()) {
            $this->_addCustomersData();
        }
        return $this;
    }
}
