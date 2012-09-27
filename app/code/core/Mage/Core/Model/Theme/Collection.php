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
 * Theme filesystem collection
 */
class Mage_Core_Model_Theme_Collection extends Varien_Data_Collection
{
    /**
     * Model of collection item
     *
     * @var string
     */
    protected $_itemObjectClass = 'Mage_Core_Model_Theme';

    /**
     * Target directory
     *
     * @var array
     */
    protected $_targetDirs = array();

    /**
     * Retrieve collection empty item
     *
     * @return Mage_Core_Model_Theme
     */
    public function getNewEmptyItem()
    {
        return Mage::getModel($this->_itemObjectClass);
    }

    /**
     * Add default pattern to themes configuration
     *
     * @param string $area
     * @return Mage_Core_Model_Theme_Collection
     */
    public function addDefaultPattern($area = 'frontend')
    {
        $this->addTargetPattern(implode(DS, array(Mage::getBaseDir('design'), $area, '*', '*', 'theme.xml')));
        return $this;
    }

    /**
     * Target directory setter. Adds directory to be scanned
     *
     * @throws Exception
     * @param string $value
     * @return Mage_Core_Model_Theme_Collection
     */
    public function addTargetPattern($value)
    {
        $this->_targetDirs[] = $value;
        return $this;
    }

    /**
     * Return target dir for themes with theme configuration file
     *
     *
     * @throws Magento_Exception
     * @return array|string
     */
    public function getTargetPatterns()
    {
        if (empty($this->_targetDirs)) {
            throw new Magento_Exception('Please specify at least one target pattern to theme config file.');
        }
        return $this->_targetDirs;
    }

    /**
     * Fill collection with theme model loaded from filesystem
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Core_Model_Theme_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $pathsToThemeConfig = array();
        foreach ($this->getTargetPatterns() as $directoryPath) {
            $pathsToThemeConfig = array_merge($pathsToThemeConfig, glob($directoryPath, GLOB_NOSORT));
        }

        $this->_loadFromFilesystem($pathsToThemeConfig);
        return $this;
    }

    /**
     * Load themes collection from file system by file list
     *
     * @param array $themeConfigPaths
     * @return Mage_Core_Model_Theme_Collection
     */
    protected function _loadFromFilesystem(array $themeConfigPaths)
    {
        foreach ($themeConfigPaths as $themeConfigPath) {
            $theme = $this->getNewEmptyItem()->loadFromConfiguration($themeConfigPath);
            $this->addItem($theme);
        }
        $this->_setIsLoaded();
        return $this;
    }

    /**
     * Retrieve item id
     *
     * @param Mage_Core_Model_Theme|Varien_Object $item
     * @return string
     */
    protected function _getItemId(Varien_Object $item)
    {
        return $item->getThemePath();
    }

    /**
     * Get items array
     *
     * @return array
     */
    public function getItemsArray()
    {
        $items = array();
        /** @var $item Mage_Core_Model_Theme */
        foreach ($this as $item) {
            $items[$item->getThemeCode()] = $item->toArray();
        }
        return $items;
    }
}
