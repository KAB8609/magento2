<?php
/**
 * Varien profiler for requests to database
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Db_Profiler extends Zend_Db_Profiler
{
    /**
     * Host IP whereto a request is sent
     *
     * @var string
     */
    protected $_host = '';

    /**
     * Database connection type
     *
     * @var string
     */
    protected $_type = '';

    /**
     * Last query Id
     *
     * @var string|null
     */
    private $_lastQueryId = null;

    /**
     * Setter for host IP
     *
     * @param string $host
     * @return Varien_Db_Profiler
     */
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }

    /**
     * Setter for database connection type
     *
     * @param string $type
     * @return Varien_Db_Profiler
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Starts a query. Creates a new query profile object (Zend_Db_Profiler_Query)
     *
     * @param string $queryText SQL statement
     * @param integer $queryType OPTIONAL Type of query, one of the Zend_Db_Profiler::* constants
     * @return integer|null
     */
    public function queryStart($queryText, $queryType = null)
    {
        $this->_lastQueryId = parent::queryStart($queryText, $queryType);
        return $this->_lastQueryId;
    }

    /**
     * Ends a query. Pass it the handle that was returned by queryStart().
     *
     * @param int $queryId
     * @return string|void
     */
    public function queryEnd($queryId)
    {
        $this->_lastQueryId = null;
        return parent::queryEnd($queryId);
    }

    /**
     * Ends the last query if exists. Used for finalize broken queries.
     *
     * @return string|void
     */
    public function queryEndLast()
    {
        if ($this->_lastQueryId !== null) {
            return $this->queryEnd($this->_lastQueryId);
        }

        return self::IGNORED;
    }
}
