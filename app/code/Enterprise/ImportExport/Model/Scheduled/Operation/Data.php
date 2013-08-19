<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation Data model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Scheduled_Operation_Data
{
    /**
     * Pending status constant
     */
    const STATUS_PENDING = 2;

    /**
     * Import/export config model
     *
     * @var Mage_ImportExport_Model_Config
     */
    protected $_importExportConfig;

    /**
     * Import entity model
     *
     * @var Mage_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_importExportConfig = isset($data['import_export_config']) ? $data['import_export_config']
            : Mage::getModel('Mage_ImportExport_Model_Config');
        $this->_importModel = isset($data['import_model']) ? $data['import_model']
            : Mage::getModel('Mage_ImportExport_Model_Import');
    }

    /**
     * Get statuses option array
     *
     * @return array
     */
    public function getStatusesOptionArray()
    {
        return array(
            1 => __('Enabled'),
            0 => __('Disabled'),
        );
    }

    /**
     * Get operations option array
     *
     * @return array
     */
    public function getOperationsOptionArray()
    {
        return array(
            'import' => __('Import'),
            'export' => __('Export')
        );
    }

    /**
     * Get frequencies option array
     *
     * @return array
     */
    public function getFrequencyOptionArray()
    {
        return array(
            Mage_Cron_Model_Config_Source_Frequency::CRON_DAILY
                => __('Daily'),
            Mage_Cron_Model_Config_Source_Frequency::CRON_WEEKLY
                => __('Weekly'),
            Mage_Cron_Model_Config_Source_Frequency::CRON_MONTHLY
                => __('Monthly'),
        );
    }

    /**
     * Get server types option array
     *
     * @return array
     */
    public function getServerTypesOptionArray()
    {
        return array(
            'file'  => __('Local Server'),
            'ftp'   => __('Remote FTP')
        );
    }

    /**
     * Get file modes option array
     *
     * @return array
     */
    public function getFileModesOptionArray()
    {
        return array(
            FTP_BINARY  => __('Binary'),
            FTP_ASCII   => __('ASCII'),
        );
    }

    /**
     * Get forced import option array
     *
     * @return array
     */
    public function getForcedImportOptionArray()
    {
        return array(
            0 => __('Stop Import'),
            1 => __('Continue Processing'),
        );
    }

    /**
     * Get operation result option array
     *
     * @return array
     */
    public function getResultOptionArray()
    {
        return array(
            0  => __('Failed'),
            1  => __('Successful'),
            self::STATUS_PENDING  => __('Pending')
        );
    }

    /**
     * Get entities option array
     *
     * @param string $type
     * @return array
     */
    public function getEntitiesOptionArray($type = null)
    {
        $importEntities = Mage_ImportExport_Model_Config::getModelsArrayOptions(
            Mage_ImportExport_Model_Import::CONFIG_KEY_ENTITIES
        );
        $exportEntities = Mage_ImportExport_Model_Config::getModelsArrayOptions(
            Mage_ImportExport_Model_Export::CONFIG_KEY_ENTITIES
        );
        switch ($type) {
            case 'import':
                return $importEntities;

            case 'export':
                return $exportEntities;

            default:
                return array_merge($importEntities, $exportEntities);
        }
    }
}
