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
namespace Magento\Pci\Model\Resource\Key;

class Change extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Encryption\EncryptionInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_structure;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Dir $dir
     * @param \Magento\Backend\Model\Config\Structure $structure
     * @param \Magento\Encryption\EncryptionInterface $encryptor
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Dir $dir,
        \Magento\Backend\Model\Config\Structure $structure,
        \Magento\Encryption\EncryptionInterface $encryptor
    ) {
        $this->_dir = $dir;
        $this->_encryptor = clone $encryptor;
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
     * TODO: seems not used
     *
     * @throws \Exception
     * @param bool $safe Specifies whether wrapping re-encryption into the database transaction or not
     */
    public function reEncryptDatabaseValues($safe = true)
    {
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
        catch (\Exception $e) {
            if ($safe) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Change encryption key
     *
     * @throws \Exception
     * @param string $key
     * @return string
     */
    public function changeEncryptionKey($key = null)
    {
        $this->_filesystem->setWorkingDirectory($this->_dir->getDir(\Magento\Core\Model\Dir::CONFIG));
        // prepare new key, encryptor and new file contents
        $file = $this->_dir->getDir(\Magento\Core\Model\Dir::CONFIG) . DS . 'local.xml';

        if (!$this->_filesystem->isWritable($file)) {
            throw new \Exception(__('File %1 is not writeable.', $file));
        }

        $contents = $this->_filesystem->read($file);
        if (null === $key) {
            $key = md5(time());
        }
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
        catch (\Exception $e) {
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
        /** @var \Magento\Backend\Model\Config\Structure $configStructure  */
        $configStructure = $this->_structure;
        $paths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Magento\Backend\Model\Config\Backend\Encrypted'
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
