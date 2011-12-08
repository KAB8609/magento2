<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backup_Model_Resource_Helper_Mssql extends Mage_Core_Model_Resource_Helper_Mssql {

    protected $_dateTypeMap  = array(
        'NUMBER'    => array(
            'tinyint', 'smallint', 'int', 'smalldatetime', 'real',
            'money', 'float', 'bit', 'decimal', 'numeric', 'smallmoney'),
        'STRING'    => array(
            'text', 'ntext', 'varchar', 'char', 'nvarchar',
            'nchar', 'sysname', 'sql_variant'),
        'DATETIME'  => array(
            'date', 'time', 'datetime2', 'datetimeoffset', 'timestamp', 'datetime')
    );

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);
        $dropTableSql = sprintf(
            "IF EXISTS (SELECT 1 FROM sys.objects WHERE object_id = OBJECT_ID(N'%s') AND type in (N'U'))\n"
            . "DROP TABLE %s\n"
            . "GO\n",
            $quotedTableName,
            $quotedTableName
        );

        return $dropTableSql;
    }

    /**
     * Retrieve foreign keys for table(s)
     *
     * @param string|null $tableName
     * @return string
     */
    public function getTableForeignKeysSql($tableName)
    {
        return $this->_getReadAdapter()->fetchOne('exec dbo.get_table_fk @objectName = ?',array($tableName));
    }

     /**
     *  Get table create script
     *
     * @param string $tableName
     * @param bool $addDropIfExists
     * @return string
     */
    public function getTableCreateScript($tableName, $addDropIfExists=false)
    {
     return $this->_getReadAdapter()->fetchOne(
            'exec dbo.get_table_dll @objectName = ?, @withfk = ?, @withdrop = ?',
            array($tableName, 1, (int)$addDropIfExists ));
    }

    /**
     * Retrieve SQL fragment for create table
     *
     * @param string $tableName
     * @param bool $withForeignKeys
     * @return string
     */
    public function getTableCreateSql($tableName, $withForeignKeys = false)
    {
        return $this->_getReadAdapter()->fetchOne(
            'exec dbo.get_table_dll @objectName = ?, @withfk = ?, @withdrop = ?',
            array($tableName, (int)$withForeignKeys, 0));
    }

    /**
     * Return scripts for procedures and functions
     *
     * @return string
     */
    protected function _getProgObjectsDefinition()
    {
        $script = '';
        $query = $this->_getReadAdapter()->query(
            "SELECT CAST(OBJECT_DEFINITION(object_id) AS VARCHAR(MAX)) AS def FROM sys.objects WHERE type IN ('FN', 'P','TR')");

        while ($row = $query->fetchColumn()) {
            $script = $script . "\nGO\n" . $row;
        }
        return $script;
    }

    /**
     * Returns SQL header data, move from original resource model
     *
     * @return string
     */
    public function getHeader()
    {
        $conf = $this->_getReadAdapter()->getConfig();
        $header = sprintf(
            "IF EXISTS (SELECT name FROM sys.databases WHERE name = N'%s')\nDROP DATABASE [%s]\n".
            "CREATE DATABASE %s\nUSE %s\nSET LANGUAGE English\n",
            $conf['dbname'], $conf['dbname'], $conf['dbname'], $conf['dbname']);
        return $header;
    }

    /**
     * Returns SQL footer data, move from original resource model
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->_getProgObjectsDefinition();
    }

    /**
     * Retrieve before insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataBeforeSql($tableName)
    {
        return '';
    }

    /**
     * Retrieve after insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataAfterSql($tableName)
    {
        return '';
    }

    /**
     * Retrieve insert SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getInsertSql($tableName)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from($tableName);
        $query   = $adapter->query($select);
        $columns = $this->_getReadAdapter()->describeTable($tableName);
        $insert  = '';
        $isIdentity = false;
        foreach ($columns as $column) {
            if ($column['IDENTITY']) {
                $isIdentity = true;
            }
        }
        while ($row = $query->fetch(Zend_Db::FETCH_ASSOC)) {
            $insRowData = array();
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $insRowData[$key] = 'NULL';
                } else {
                    if (in_array($columns[$key]['DATA_TYPE'], $this->_dateTypeMap['STRING'])) {
                        $insRowData[$key] = sprintf("'%s'", str_replace("'","''",$value) );
                    } elseif (!(array_search($columns[$key]['DATA_TYPE'], $this->_dateTypeMap['DATETIME']) === false)) {
                        $insRowData[$key] = sprintf("CAST('%s' AS %s)", $value, $columns[$key]['DATA_TYPE']);
                    } else {
                        $insRowData[$key] = $value;
                    }
                }
            }
            $insert .= sprintf("INSERT INTO %s (%s) VALUES (%s)\n",
                $tableName,
                implode(',', array_keys($columns)),
                implode(',', $insRowData));
        }

        if ($isIdentity) {
            $insert = sprintf("SET IDENTITY_INSERT %s ON\n%s\nSET IDENTITY_INSERT %s OFF",
                $tableName, $insert, $tableName);
        }

        return $insert;
    }

    /*
     * Turn on serializable mode
     */
    public function turnOnSerializableMode()
    {
        $this->_getReadAdapter()->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
    }

    /*
     * Turn on read committed mode
     */
    public function turnOnReadCommittedMode()
    {
        $this->_getReadAdapter()->query("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
    }
}
