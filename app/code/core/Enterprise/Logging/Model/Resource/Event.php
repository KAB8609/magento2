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
 * Logging event resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Class constructor
     *
     * @param Mage_Core_Model_Resource $resource
     * @param Magento_Filesystem $filesystem
     * @throws InvalidArgumentException
     */
    public function __construct(Mage_Core_Model_Resource $resource, Magento_Filesystem $filesystem)
    {
        parent::__construct($resource);
        $this->_filesystem = $filesystem;
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_logging_event', 'log_id');
    }

    /**
     * Convert data before save ip
     *
     * @param Mage_Core_Model_Abstract $event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $event)
    {
        $event->setData('ip', ip2long($event->getIp()));
        $event->setTime($this->formatDate($event->getTime()));
    }

    /**
     * Rotate logs - get from database and pump to CSV-file
     *
     * @param int $lifetime
     */
    public function rotate($lifetime)
    {
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        $table = $this->getTable('enterprise_logging_event');

        // get the latest log entry required to the moment
        $clearBefore = $this->formatDate(time() - $lifetime);

        $select = $readAdapter->select()
            ->from($this->getMainTable(), 'log_id')
            ->where('time < ?', $clearBefore)
            ->order('log_id DESC')
            ->limit(1);
        $latestLogEntry = $readAdapter->fetchOne($select);
        if ($latestLogEntry) {
            // make sure folder for dump file will exist
            /** @var Enterprise_Logging_Model_Archive $archive */
            $archive = Mage::getModel('Enterprise_Logging_Model_Archive');
            $archive->createNew();

            $expr = Mage::getResourceHelper('Enterprise_Logging')->getInetNtoaExpr('ip');
            $select = $readAdapter->select()
                ->from($this->getMainTable())
                ->where('log_id <= ?', $latestLogEntry)
                ->columns($expr);

            $rows = $readAdapter->fetchAll($select);

            $stream = $this->_filesystem->createAndOpenStream($archive->getFilename(), 'w');
            if (!$stream->open()) {
                throw Exception();
            }
            // dump all records before this log entry into a CSV-file
            foreach ($rows as $row) {
                $stream->writeCsv($row);
            }
            $stream->close();

            $writeAdapter->delete($this->getMainTable(), array('log_id <= ?' => $latestLogEntry));
        }
    }

    /**
     * Select all values of specified field from main table
     *
     * @param string $field
     * @param bool $order
     * @return array
     */
    public function getAllFieldValues($field, $order = true)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from($this->getMainTable(), $field);
        if (!is_null($order)) {
            $select->order($field . ($order ? '' : ' DESC'));
        }
        return $adapter->fetchCol($select);
    }

    /**
     * Get all admin usernames that are currently in event log table
     * Possible SQL-performance issue
     *
     * @return array
     */
    public function getUserNames()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->distinct()
            ->from(array('admins' => $this->getTable('admin_user')), 'username')
            ->joinInner(
                array('events' => $this->getTable('enterprise_logging_event')),
                'admins.username = events.' . $adapter->quoteIdentifier('user'),
                array());
        return $adapter->fetchCol($select);
    }

    /**
     * Get event change ids of specified event
     *
     * @param int $eventId
     * @return array
     */
    public function getEventChangeIds($eventId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('enterprise_logging_event_changes'), array('id'))
            ->where('event_id = ?', $eventId);
        return $adapter->fetchCol($select);
    }
}
