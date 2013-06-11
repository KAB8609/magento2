<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme files model class
 *
 * @method int getThemeId()
 * @method string getFileType()
 * @method string getContent()
 * @method string getOrder()
 * @method bool getIsTemporary()
 * @method setThemeId(int $id)
 * @method setFileName(string $filename)
 * @method setFileType(string $type)
 * @method setContent(string $content)
 * @method setSortOrder(string $order)
 * @method Mage_Core_Model_Theme_File setUpdatedAt($time)
 * @method Mage_Core_Model_Theme_File setLayoutLinkId($id)
 * @method string getFilePath() Relative path to file
 * @method int getLayoutLinkId()
 */
class Mage_Core_Model_Theme_File extends Mage_Core_Model_Abstract
{
    /**
     * Css file type
     */
    const TYPE_CSS = 'css';

    /**
     * Js file type
     */
    const TYPE_JS = 'js';

    /**
     * @var Varien_Io_File
     */
    protected $_ioFile;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Varien_Io_File $ioFile
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Varien_Io_File $ioFile,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);

        $this->_ioFile = $ioFile;
        $this->_objectManager = $objectManager;
    }

    /**
     * Theme files model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme_File');
    }

    /**
     * Get theme model
     *
     * @return Mage_Core_Model_Theme
     * @throws Magento_Exception
     */
    public function getTheme()
    {
        if ($this->hasData('theme')) {
            return $this->getData('theme');
        }

        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        $themeId = $this->getData('theme_id');
        if ($themeId && $theme->load($themeId)->getId()) {
            $this->setData('theme', $theme);
        } else {
            throw new Magento_Exception('Theme id should be set');
        }
        return $theme;
    }

    /**
     * Create/update/delete file after save
     * Delete file if only file is empty
     *
     * @return Mage_Core_Model_Theme_File
     */
    protected function _afterSave()
    {
        if ($this->hasContent()) {
            $this->_saveFile();
        } else {
            $this->delete();
        }
        return parent::_afterSave();
    }

    /**
     * Delete file form file system after delete form db
     *
     * @return Mage_Core_Model_Theme_File
     */
    protected function _afterDelete()
    {
        $this->_deleteFile();
        return parent::_afterDelete();
    }

    /**
     * Create/update file in file system
     *
     * @return bool|int
     */
    protected function _saveFile()
    {
        $filePath = $this->getFullPath();
        $this->_ioFile->checkAndCreateFolder(dirname($filePath));
        $result = $this->_ioFile->write($filePath, $this->getContent());
        return $result;
    }

    /**
     * Delete file form file system
     *
     * @return bool
     */
    protected function _deleteFile()
    {
        $result = $this->_ioFile->rm($this->getFullPath());
        return $result;
    }

    /**
     * Check if file has content
     *
     * @return bool
     */
    public function hasContent()
    {
        return (bool)trim($this->getContent());
    }

    /**
     * Get file name of customization file
     *
     * @return string
     */
    public function getFileName()
    {
        return basename($this->getFilePath());
    }

    /**
     * Return absolute path to file of customization
     *
     * @return null|string
     */
    public function getFullPath()
    {
        $path = null;
        if ($this->getId()) {
            $path = $this->getTheme()->getCustomizationPath() . DIRECTORY_SEPARATOR . $this->getFilePath();
            $path = Magento_Filesystem::fixSeparator($path);
        }
        return $path;
    }

    /**
     * Retrieve a page asset representing a theme file
     *
     * @return Mage_Core_Model_Page_Asset_AssetInterface|null
     */
    public function getAsset()
    {
        if ($this->hasContent()) {
            return $this->_objectManager->create(
                'Mage_Core_Model_Page_Asset_PublicFile',
                array('file' => $this->getFullPath(), 'contentType' => $this->getFileType())
            );
        }
        return null;
    }

    /**
     * Get file id and file name
     *
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getFileName(),
            'temporary' => ($this->getIsTemporary() ? $this->getId() : '0')
        );
    }
}
