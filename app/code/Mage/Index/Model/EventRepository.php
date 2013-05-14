<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Index_Model_EventRepository
{
    /**
     * Event collection factory
     *
     * @var Mage_Index_Model_Resource_Event_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Mage_Index_Model_Resource_Event_CollectionFactory $collectionFactory
     */
    public function __construct(Mage_Index_Model_Resource_Event_CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Check whether unprocessed events exist for provided process
     *
     * @param int|array|Mage_Index_Model_Process $process
     * @return bool
     */
    public function hasUnprocessed($process)
    {
        return (bool) $this->getUnprocessed($process)->getSize();
    }

    /**
     * Retrieve list of unprocessed events
     *
     * @param int|array|Mage_Index_Model_Process $process
     * @return Mage_Index_Model_Resource_Event_Collection
     */
    public function getUnprocessed($process)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addProcessFilter($process, Mage_Index_Model_Process::EVENT_STATUS_NEW);
        return $collection;
    }
}
