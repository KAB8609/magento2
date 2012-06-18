<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import entity abstract model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ImportExport_Model_Import_Entity_V2_Abstract
{
    /**#@+
     * Database constants
     */
    const DB_MAX_PACKET_COEFFICIENT = 900000;
    const DB_MAX_PACKET_DATA        = 1048576;
    const DB_MAX_VARCHAR_LENGTH     = 256;
    const DB_MAX_TEXT_LENGTH        = 65536;
    /**#@-*/

    /**
     * DB connection.
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Has data process validation done?
     *
     * @var bool
     */
    protected $_dataValidated = false;

    /**
     * DB data source model
     *
     * @var Mage_ImportExport_Model_Resource_Import_Data
     */
    protected $_dataSourceModel;

    /**
     * Error codes with arrays of corresponding row numbers
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Error counter
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Limit of errors after which pre-processing will exit
     *
     * @var int
     */
    protected $_errorsLimit = 100;

    /**
     * Flag to disable import
     *
     * @var bool
     */
    protected $_importAllowed = true;

    /**
     * Array of invalid rows numbers
     *
     * @var array
     */
    protected $_invalidRows = array();

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * Notice messages
     *
     * @var array
     */
    protected $_notices = array();

    /**
     * Entity model parameters
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_particularAttributes = array();

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array();

    /**
     * Number of entities processed by validation
     *
     * @var int
     */
    protected $_processedEntitiesCount = 0;

    /**
     * Number of rows processed by validation
     *
     * @var int
     */
    protected $_processedRowsCount = 0;

    /**
     * Rows which will be skipped during import
     *
     * [Row number 1] => true,
     * ...
     * [Row number N] => true
     *
     * @var array
     */
    protected $_rowsToSkip = array();

    /**
     * Array of numbers of validated rows as keys and boolean TRUE as values
     *
     * @var array
     */
    protected $_validatedRows = array();

    /**
     * Source model
     *
     * @var Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected $_source;

    /**
     * Array of unique attributes
     *
     * @var array
     */
    protected $_uniqueAttributes = array();

    /**
     * List of available behaviors
     *
     * @var array
     */
    protected $_availableBehaviors = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_dataSourceModel = Mage_ImportExport_Model_Import::getDataSourceModel();
        /** @var $coreResourceHelper Mage_Core_Model_Resource */
        $coreResourceHelper = Mage::getSingleton('Mage_Core_Model_Resource');
        $this->_connection = $coreResourceHelper->getConnection('write');

        $this->_availableBehaviors = array(
            Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
            Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE,
            Mage_ImportExport_Model_Import::BEHAVIOR_DELETE,
        );
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    abstract protected function _importData();

    /**
     * Imported entity type code getter
     *
     * @abstract
     * @return string
     */
    abstract public function getEntityTypeCode();

    /**
     * Change row data before saving in DB table
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        /**
         * Convert all empty strings to null values, as
         * a) we don't use empty string in DB
         * b) empty strings instead of numeric values will product errors in Sql Server
         */
        foreach ($rowData as $key => $val) {
            if ($val === '') {
                $rowData[$key] = null;
            }
        }
        return $rowData;
    }

    /**
     * Validate data rows and save bunches to DB
     *
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected function _saveValidatedBunches()
    {
        $source            = $this->getSource();
        $processedDataSize = 0;
        $bunchRows         = array();
        $startNewBunch     = false;
        $nextRowBackup     = array();

        /** @var $resourceHelper Mage_ImportExport_Model_Resource_Helper_Mssql */
        $resourceHelper = Mage::getResourceHelper('Mage_ImportExport');
        /** @var $dataHelepr  Mage_ImportExport_Helper_Data*/
        $dataHelepr = Mage::helper('Mage_ImportExport_Helper_Data');
        $bunchSize = $dataHelepr->getBunchSize();

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);

                $bunchRows         = $nextRowBackup;
                $processedDataSize = strlen(serialize($bunchRows));
                $startNewBunch     = false;
                $nextRowBackup     = array();
            }
            if ($source->valid()) {
                // errors limit check
                if ($this->_errorsCount >= $this->_errorsLimit) {
                    return;
                }
                $rowData = $source->current();
                // add row to bunch for save
                if ($this->validateRow($rowData, $source->key())) {
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($rowData));

                    $isBunchSizeExceeded = ($bunchSize > 0 && count($bunchRows) >= $bunchSize);

                    if (($processedDataSize + $rowSize) >= $resourceHelper->getMaxDataSize() || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = array($source->key() => $rowData);
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $processedDataSize += $rowSize;
                    }
                }
                $this->_processedRowsCount++;
                $source->next();
            }
        }
        return $this;
    }

    /**
     * Add error with corresponding current data source row number
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     * @param string $colName OPTIONAL Column name.
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    public function addRowError($errorCode, $errorRowNum, $colName = null)
    {
        $this->_errors[$errorCode][] = array($errorRowNum + 1, $colName); // one added for human readability
        $this->_invalidRows[$errorRowNum] = true;
        $this->_errorsCount++;

        return $this;
    }

    /**
     * Add message template for specific error code from outside
     *
     * @param string $errorCode Error code
     * @param string $message Message template
     * @return Mage_ImportExport_Model_Import_Entity_V2_Abstract
     */
    public function addMessageTemplate($errorCode, $message)
    {
        $this->_messageTemplates[$errorCode] = $message;

        return $this;
    }

    /**
     * Import behavior getter
     *
     * @return string
     */
    public function getBehavior()
    {
        if (isset($this->_parameters['behavior'])
            && in_array($this->_parameters['behavior'], $this->_availableBehaviors)
        ) {
            return $this->_parameters['behavior'];
        }
        return Mage_ImportExport_Model_Import::getDefaultBehavior();
    }

    /**
     * Returns error information grouped by error types and translated (if possible)
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $translator = Mage::helper('Mage_ImportExport_Helper_Data');
        $messages   = array();

        foreach ($this->_errors as $errorCode => $errorRows) {
            if (isset($this->_messageTemplates[$errorCode])) {
                $errorCode = $translator->__($this->_messageTemplates[$errorCode]);
            }
            foreach ($errorRows as $errorRowData) {
                $key = $errorRowData[1] ? sprintf($errorCode, $errorRowData[1]) : $errorCode;
                $messages[$key][] = $errorRowData[0];
            }
        }
        return $messages;
    }

    /**
     * Returns error counter value
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_errorsCount;
    }

    /**
     * Returns error limit value
     *
     * @return int
     */
    public function getErrorsLimit()
    {
        return $this->_errorsLimit;
    }

    /**
     * Returns invalid rows count
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return count($this->_invalidRows);
    }

    /**
     * Returns model notices
     *
     * @return array
     */
    public function getNotices()
    {
        return $this->_notices;
    }

    /**
     * Returns number of checked entities
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_processedEntitiesCount;
    }

    /**
     * Returns number of checked rows
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_processedRowsCount;
    }

    /**
     * Source object getter
     *
     * @throws Exception
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    public function getSource()
    {
        if (!$this->_source) {
            Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('Source is not set'));
        }
        return $this->_source;
    }

    /**
     * Import process start
     *
     * @return bool Result of operation.
     */
    public function importData()
    {
        return $this->_importData();
    }

    /**
     * Is attribute contains particular data (not plain entity attribute)
     *
     * @param string $attrCode
     * @return bool
     */
    public function isAttributeParticular($attrCode)
    {
        return in_array($attrCode, $this->_particularAttributes);
    }

    /**
     * Check one attribute. Can be overridden in child.
     *
     * @param string $attributeCode Attribute code
     * @param array $attributeParams Attribute params
     * @param array $rowData Row data
     * @param int $rowNumber
     * @return boolean
     */
    public function isAttributeValid($attributeCode, array $attributeParams, array $rowData, $rowNumber)
    {
        /** @var $coreHelper Mage_Core_Helper_String */
        $coreHelper = Mage::helper('Mage_Core_Helper_String');

        switch ($attributeParams['type']) {
            case 'varchar':
                $val   = $coreHelper->cleanString($rowData[$attributeCode]);
                $valid = $coreHelper->strlen($val) < self::DB_MAX_VARCHAR_LENGTH;
                break;
            case 'decimal':
                $val   = trim($rowData[$attributeCode]);
                $valid = ((float)$val == $val) && is_numeric($val);
                break;
            case 'select':
            case 'multiselect':
                $valid = isset($attributeParams['options'][strtolower($rowData[$attributeCode])]);
                break;
            case 'int':
                $val   = trim($rowData[$attributeCode]);
                $valid = ((int)$val == $val) && is_numeric($val);
                break;
            case 'datetime':
                $val   = trim($rowData[$attributeCode]);
                $valid = strtotime($val) !== false
                    || preg_match('/^\d{2}.\d{2}.\d{2,4}(?:\s+\d{1,2}.\d{1,2}(?:.\d{1,2})?)?$/', $val);
                break;
            case 'text':
                $val   = $coreHelper->cleanString($rowData[$attributeCode]);
                $valid = $coreHelper->strlen($val) < self::DB_MAX_TEXT_LENGTH;
                break;
            default:
                $valid = true;
                break;
        }

        /** @var $dataHelper Mage_ImportExport_Helper_Data */
        $dataHelper = Mage::helper('Mage_ImportExport_Helper_Data');

        if (!$valid) {
            $this->addRowError($dataHelper->__("Invalid value for '%s'"), $rowNumber, $attributeCode);
        } elseif (!empty($attributeParams['is_unique'])) {
            if (isset($this->_uniqueAttributes[$attributeCode][$rowData[$attributeCode]])) {
                $this->addRowError($dataHelper->__("Duplicate Unique Attribute for '%s'"), $rowNumber, $attributeCode);
                return false;
            }
            $this->_uniqueAttributes[$attributeCode][$rowData[$attributeCode]] = true;
        }
        return (bool) $valid;
    }

    /**
     * Check that is all of data valid
     *
     * @return bool
     */
    public function isDataValid()
    {
        $this->validateData();
        return 0 == $this->_errorsCount;
    }

    /**
     * Import possibility getter
     *
     * @return bool
     */
    public function isImportAllowed()
    {
        return $this->_importAllowed;
    }

    /**
     * Returns TRUE if row is valid and not in skipped rows array
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function isRowAllowedToImport(array $rowData, $rowNum)
    {
        return $this->validateRow($rowData, $rowNum) && !isset($this->_rowsToSkip[$rowNum]);
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    abstract public function validateRow(array $rowData, $rowNum);

    /**
     * Set data from outside to change behavior
     *
     * @param array $params
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    public function setParameters(array $params)
    {
        $this->_parameters = $params;
        return $this;
    }

    /**
     * Source model setter
     *
     * @param Mage_ImportExport_Model_Import_Adapter_Abstract $source
     * @return Mage_ImportExport_Model_Import_Entity_V2_Abstract
     */
    public function setSource(Mage_ImportExport_Model_Import_Adapter_Abstract $source)
    {
        $this->_source = $source;
        $this->_dataValidated = false;

        return $this;
    }

    /**
     * Validate data
     *
     * @throws Exception
     * @return Mage_ImportExport_Model_Import_Entity_V2_Abstract
     */
    public function validateData()
    {
        if (!$this->_dataValidated) {
            /** @var $dataHelper Mage_ImportExport_Helper_Data */
            $dataHelper = Mage::helper('Mage_ImportExport_Helper_Data');

            // does all permanent columns exists?
            if (($colsAbsent = array_diff($this->_permanentAttributes, $this->getSource()->getColNames()))) {
                Mage::throwException(
                    $dataHelper->__('Can not find required columns: %s', implode(', ', $colsAbsent))
                );
            }

            // initialize validation related attributes
            $this->_errors = array();
            $this->_invalidRows = array();

            // check attribute columns names validity
            $invalidColumns = array();

            foreach ($this->getSource()->getColNames() as $columnName) {
                if (!preg_match('/^[a-z][a-z0-9_]*$/', $columnName) && !$this->isAttributeParticular($columnName)) {
                    $invalidColumns[] = $columnName;
                }
            }
            if ($invalidColumns) {
                Mage::throwException(
                    $dataHelper->__('Column names: "%s" are invalid', implode('", "', $invalidColumns))
                );
            }
            $this->_saveValidatedBunches();

            $this->_dataValidated = true;
        }
        return $this;
    }
}
