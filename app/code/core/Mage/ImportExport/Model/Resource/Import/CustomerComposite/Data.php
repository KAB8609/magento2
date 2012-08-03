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
 * ImportExport customer_composite entity import data abstract resource model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Resource_Import_CustomerComposite_Data
    extends Mage_ImportExport_Model_Resource_Import_Data
{
    /**
     * Entity type
     *
     * @var string
     */
    protected $_entityType = Mage_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER;

    /**
     * Customer attributes
     *
     * @var array
     */
    protected $_customerAttributes = array();

    /**
     * Class constructor
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        parent::__construct($arguments);

        if (isset($arguments['entity_type'])) {
            $this->_entityType = $arguments['entity_type'];
        }
        if (isset($arguments['customer_attributes'])) {
            $this->_customerAttributes = $arguments['customer_attributes'];
        }
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        $bunchRows = parent::getNextBunch();
        if ($bunchRows != null) {
            $rows = array();
            foreach ($bunchRows as $row) {
                $row = $this->_prepareRow($row);
                if ($row !== null) {
                    unset($row['_scope']);
                    $rows[] = $row;
                }
            }
            return $rows;
        } else {
            return $bunchRows;
        }
    }

    /**
     * Prepare row
     *
     * @param array $rowData
     * @internal param array $data
     * @return array
     */
    protected function _prepareRow(array $rowData)
    {
        if ($this->_entityType == Mage_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER) {
            if ($rowData['_scope'] == Mage_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT) {
                return $rowData;
            } else {
                return null;
            }
        } else {
            return $this->_prepareAddressRowData($rowData);
        }
    }

    /**
     * Prepare data row for address entity validation or import
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareAddressRowData(array $rowData)
    {
        $excludedAttributes = array(
            Mage_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING,
            Mage_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING
        );
        $prefix = Mage_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX;

        $result = array();
        foreach ($rowData as $key => $value) {
            if (!in_array($key, $this->_customerAttributes)) {
                if (!in_array($key, $excludedAttributes)) {
                    $key = str_replace($prefix, '', $key);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
