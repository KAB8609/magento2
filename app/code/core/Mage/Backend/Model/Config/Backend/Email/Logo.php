<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Backend model for uploading transactional emails custom logo image
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Email_Logo extends Mage_Backend_Model_Config_Backend_Image
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR                = 'email/logo';

    /**
     * Token for the root part of directory path for uploading
     */
    const UPLOAD_ROOT_TOKEN         = 'system/filesystem/media';

    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize         = 2048;

    /**
     * Return path to directory for upload file
     *
     * @return string
     */
    protected function _getUploadDir()
    {
        $uploadDir  = $this->_appendScopeInfo(self::UPLOAD_DIR);
        $uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN);
        $uploadDir  = $uploadRoot . DS . $uploadDir;
        return $uploadDir;
    }

    /**
     * Makes a decision about whether to add info about the scope
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Save uploaded file before saving config value
     *
     * Save changes and delete file if "delete" option passed
     *
     * @return Mage_Backend_Model_Config_Backend_Email_Logo
     */
    protected function _beforeSave()
    {
        $value       = $this->getValue();
        $deleteFlag  = (is_array($value) && !empty($value['delete']));
        $fileTmpName = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];

        if ($this->getOldValue() && ($fileTmpName || $deleteFlag)) {
            $io = new Varien_Io_File();
            $io->rm($this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN) . DS . self::UPLOAD_DIR . DS . $this->getOldValue());
        }
        return parent::_beforeSave();
    }
}
