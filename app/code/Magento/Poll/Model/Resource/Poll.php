<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Poll resource model
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Poll\Model\Resource;

class Poll extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('poll', 'poll_id');
    }

    /**
     * Initialize unique fields
     *
     * @return \Magento\Poll\Model\Resource\Poll
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'poll_title',
            'title' => __('Poll with the same question')
        ));
        return $this;
    }

    /**
     * Get select object for not closed poll ids
     *
     * @param \Magento\Poll\Model\Poll $object
     * @return
     */
    protected function _getSelectIds($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('main_table'=>$this->getMainTable()), $this->getIdFieldName())
            ->where('closed = ?', 0);

        $excludeIds = $object->getExcludeFilter();
        if ($excludeIds) {
            $select->where('main_table.poll_id NOT IN(?)', $excludeIds);
        }

        $storeId = $object->getStoreFilter();
        if ($storeId) {
            $select->join(
                array('store' => $this->getTable('poll_store')),
                'main_table.poll_id=store.poll_id AND store.store_id = ' . $read->quote($storeId),
                array()
            );
        }

        return $select;
    }

    /**
     * Get random identifier not closed poll
     *
     * @param \Magento\Poll\Model\Poll $object
     * @return int
     */
    public function getRandomId($object)
    {
        $select = $this->_getSelectIds($object)->orderRand()->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Get all ids for not closed polls
     *
     * @param \Magento\Poll\Model\Poll $object
     * @return array
     */
    public function getAllIds($object)
    {
        $select = $this->_getSelectIds($object);
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Check answer id existing for poll
     *
     * @param \Magento\Poll\Model\Poll $poll
     * @param int $answerId
     * @return bool
     */
    public function checkAnswerId($poll, $answerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('poll_answer'), 'answer_id')
            ->where('poll_id = :poll_id')
            ->where('answer_id = :answer_id');
        $bind = array(':poll_id' => $poll->getId(), ':answer_id' => $answerId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Get voted poll ids by specified IP-address
     * Will return non-empty only if appropriate option in config is enabled
     * If poll id is not empty, it will look only for records with specified value
     *
     * @param string $ipAddress
     * @param int $pollId
     * @return array
     */
    public function getVotedPollIdsByIp($ipAddress, $pollId = false)
    {
        // check if validation by ip is enabled
        if (!\Mage::getModel('Magento\Poll\Model\Poll')->isValidationByIp()) {
            return array();
        }

        // look for ids in database
        $select = $this->_getReadAdapter()->select()
            ->distinct()
            ->from($this->getTable('poll_vote'), 'poll_id')
            ->where('ip_address = :ip_address');
        $bind = array(':ip_address' => ip2long($ipAddress));
        if (!empty($pollId)) {
            $select->where('poll_id = :poll_id');
            $bind[':poll_id'] = $pollId;
        }
        $result = $this->_getReadAdapter()->fetchCol($select, $bind);
        if (empty($result)) {
            $result = array();
        }
        return $result;
    }

    /**
     * Resett votes count
     *
     * @param \Magento\Poll\Model\Poll $object
     * @return \Magento\Poll\Model\Poll
     */
    public function resetVotesCount($object)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getTable('poll_answer'), new \Zend_Db_Expr("SUM(votes_count)"))
            ->where('poll_id = ?', $object->getPollId());
        $adapter->update(
            $this->getMainTable(),
            array('votes_count' => new \Zend_Db_Expr("($select)")),
            array('poll_id = ' . $adapter->quote($object->getPollId()))
        );
        return $object;
    }

    /**
     * Load store Ids array
     *
     * @param \Magento\Poll\Model\Poll $object
     */
    public function loadStoreIds(\Magento\Poll\Model\Poll $object)
    {
        $pollId   = $object->getId();
        $storeIds = array();
        if ($pollId) {
            $storeIds = $this->lookupStoreIds($pollId);
        }
        $object->setStoreIds($storeIds);
    }

    /**
     * Delete current poll from the table poll_store and then
     * insert to update "poll to store" relations
     *
     * @param \Magento\Core\Model\AbstractModel $object
     */
    public function _afterSave(\Magento\Core\Model\AbstractModel $object)
    {
        /** stores */
        $deleteWhere = $this->_getWriteAdapter()->quoteInto('poll_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('poll_store'), $deleteWhere);

        foreach ($object->getStoreIds() as $storeId) {
            $pollStoreData = array(
            'poll_id'   => $object->getId(),
            'store_id'  => $storeId
            );
            $this->_getWriteAdapter()->insert($this->getTable('poll_store'), $pollStoreData);
        }

        /** answers */
        foreach ($object->getAnswers() as $answer) {
            $answer->setPollId($object->getId());
            $answer->save();
        }
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
                ->from($this->getTable('poll_store'), 'store_id')
                ->where("{$this->getIdFieldName()} = :id_field"),
            array(':id_field' => $id)
        );
    }
}
