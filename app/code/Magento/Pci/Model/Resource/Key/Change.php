<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Encryption key changer resource model
 * The operation must be done in one transaction
 *
 * @category    Magento
 * @package     Magento_Pci
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pci_Model_Resource_Key_Change extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Pci_Model_Encryption
     */
    protected $_encryptor;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_structure;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Backend_Model_Config_Structure $structure
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Resource $resource,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Dir $dir,
        Magento_Backend_Model_Config_Structure $structure
    ) {
        $this->_coreData = $coreData;
        $this->_dir = $dir;
        parent::__construct($resource);
        $this->_filesystem = $filesystem;
        $this->_structure = $structure;
    }

    /**
     * Initialize
     *
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'config_id');
    }

    /**
     * Re-encrypt all encrypted data in the database
     *
     * @throws Exception
     * @param bool $safe Specifies whether wrapping re-encryption into the database transaction or not
     */
    public function reEncryptDatabaseValues($safe = true)
    {
        $this->_encryptor = clone $this->_coreData->getEncryptor();

        // update database only
        if ($safe) {
            $this->beginTransaction();
        }
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            if ($safe) {
                $this->commit();
            }
        }
        catch (Exception $e) {
            if ($safe) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Change encryption key
     *
     * @throws Exception
     * @param string $key
     * @return string
     */
    public function changeEncryptionKey($key = null)
    {
        $this->_filesystem->setWorkingDirectory($this->_dir->getDir(Magento_Core_Model_Dir::CONFIG));
        // prepare new key, encryptor and new file contents
        $file = $this->_dir->getDir(Magento_Core_Model_Dir::CONFIG) . DS . 'local.xml';

        if (!$this->_filesystem->isWritable($file)) {
            throw new Exception(__('File %1 is not writeable.', $file));
        }

        $contents = $this->_filesystem->read($file);
        if (null === $key) {
            $key = md5(time());
        }
        $this->_encryptor = clone $this->_coreData->getEncryptor();
        $this->_encryptor->setNewKey($key);
        $contents = preg_replace('/<key><\!\[CDATA\[(.+?)\]\]><\/key>/s', 
            '<key><![CDATA[' . $this->_encryptor->exportKeys() . ']]></key>', $contents
        );

        // update database and local.xml
        $this->beginTransaction();
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            $this->_filesystem->write($file, $contents);
            $this->commit();
            return $key;
        }
        catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Gather all encrypted system config values and re-encrypt them
     *
     */
    protected function _reEncryptSystemConfigurationValues()
    {
        // look for encrypted node entries in all system.xml files
        /** @var Magento_Backend_Model_Config_Structure $configStructure  */
        $configStructure = $this->_structure;
        $paths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Magento_Backend_Model_Config_Backend_Encrypted'
        );

        // walk through found data and re-encrypt it
        if ($paths) {
            $table = $this->getTable('core_config_data');
            $values = $this->_getReadAdapter()->fetchPairs($this->_getReadAdapter()->select()
                ->from($table, array('config_id', 'value'))
                ->where('path IN (?)', $paths)
                ->where('value NOT LIKE ?', '')
            );
            foreach ($values as $configId => $value) {
                $this->_getWriteAdapter()->update($table,
                    array('value' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                    array('config_id = ?' => (int)$configId)
                );
            }
        }
    }

    /**
     * Gather saved credit card numbers from sales order payments and re-encrypt them
     *
     */
    protected function _reEncryptCreditCardNumbers()
    {
        $table = $this->getTable('sales_flat_order_payment');
        $select = $this->_getWriteAdapter()->select()
            ->from($table, array('entity_id', 'cc_number_enc'));

        $attributeValues = $this->_getWriteAdapter()->fetchPairs($select);
        // save new values
        foreach ($attributeValues as $valueId => $value) {
            $this->_getWriteAdapter()->update($table,
                array('cc_number_enc' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                array('entity_id = ?' => (int)$valueId)
            );
        }
    }
}
