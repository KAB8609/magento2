<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sitemap model
 *
 * @method Mage_Sitemap_Model_Resource_Sitemap _getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap getResource()
 * @method string getSitemapType()
 * @method Mage_Sitemap_Model_Sitemap setSitemapType(string $value)
 * @method string getSitemapFilename()
 * @method Mage_Sitemap_Model_Sitemap setSitemapFilename(string $value)
 * @method string getSitemapPath()
 * @method Mage_Sitemap_Model_Sitemap setSitemapPath(string $value)
 * @method string getSitemapTime()
 * @method Mage_Sitemap_Model_Sitemap setSitemapTime(string $value)
 * @method int getStoreId()
 * @method Mage_Sitemap_Model_Sitemap setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Sitemap extends Mage_Core_Model_Abstract
{
    const OPEN_TAG_KEY = 'start';
    const CLOSE_TAG_KEY = 'end';
    const INDEX_FILE_PREFIX = 'sitemap';
    const TYPE_INDEX = 'sitemap';
    const TYPE_URL = 'url';

    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * File handler
     *
     * @var Varien_Io_File
     */
    protected $_fileHandler;

    /**
     * Sitemap items
     *
     * @var array
     */
    protected $_sitemapItems = array();

    /**
     * Current sitemap increment
     *
     * @var int
     */
    protected $_sitemapIncrement = 0;

    /**
     * Sitemap start and end tags
     *
     * @var array
     */
    protected $_tags = array();

    /**
     * Number of lines in sitemap
     *
     * @var int
     */
    protected $_lineCount = 0;

    /**
     * Current sitemap file size
     *
     * @var int
     */
    protected $_fileSize = 0;

    /**
     * New line possible symbols
     *
     * @var array
     */
    private $_crlf = array("win" => "\r\n", "unix" => "\n", "mac" => "\r");

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sitemap_Model_Resource_Sitemap');
    }

    /**
     * Get file handler
     *
     * @throws Mage_Core_Exception
     * @return Varien_Io_File
     */
    protected function _getFileHandler()
    {
        if ($this->_fileHandler) {
            return $this->_fileHandler;
        } else {
            Mage::throwException(Mage::helper('Mage_Sitemap_Helper_Data')->__('File handler unreachable'));
        }
    }

    /**
     * Initialize sitemap items
     */
    protected function _initSitemapItems()
    {
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        $storeId = $this->getStoreId();

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getCategoryChangefreq($storeId),
            'priority' => $helper->getCategoryPriority($storeId),
            'collection' => $this->_getCategoryItemsCollection($storeId)
        ));

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getProductChangefreq($storeId),
            'priority' => $helper->getProductPriority($storeId),
            'collection' => $this->_getProductItemsCollection($storeId)
        ));

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getPageChangefreq($storeId),
            'priority' => $helper->getPagePriority($storeId),
            'collection' => $this->_getPageItemsCollection($storeId)
        ));

        $this->_tags = array(
            self::TYPE_INDEX => array(
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                    . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL,
                self::CLOSE_TAG_KEY => '</sitemapindex>'
            ),
            self::TYPE_URL => array(
                self::OPEN_TAG_KEY => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                    . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL,
                self::CLOSE_TAG_KEY => '</urlset>'
            )
        );
    }

    /**
     * Get category items collection
     *
     * @param int $storeId
     * @return array
     */
    protected function _getCategoryItemsCollection($storeId)
    {
        return Mage::getResourceModel('Mage_Sitemap_Model_Resource_Catalog_Category')->getCollection($storeId);
    }

    /**
     * Get product items collection
     *
     * @param int $storeId
     * @return array
     */
    protected function _getProductItemsCollection($storeId)
    {
        return Mage::getResourceModel('Mage_Sitemap_Model_Resource_Catalog_Product')->getCollection($storeId);
    }

    /**
     * Get cms page items collection
     *
     * @param int $storeId
     * @return array
     */
    protected function _getPageItemsCollection($storeId)
    {
        return Mage::getResourceModel('Mage_Sitemap_Model_Resource_Cms_Page')->getCollection($storeId);
    }

    /**
     * Check sitemap file location and permissions
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $file = $this->_getFileObject();
        $realPath = $file->getCleanPath($this->_getBaseDir() . '/' . $this->getSitemapPath());

        /**
         * Check path is allow
         */
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        if (!$file->allowedPath($realPath, $this->_getBaseDir())) {
            Mage::throwException($helper->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$file->fileExists($realPath, false)) {
            Mage::throwException($helper->__('Please create the specified folder "%s" before saving the sitemap.',
                Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->getSitemapPath())));
        }

        if (!$file->isWriteable($realPath)) {
            Mage::throwException($helper->__('Please make sure that "%s" is writable by web-server.',
                $this->getSitemapPath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            Mage::throwException($helper->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->setSitemapPath(
            rtrim(str_replace(str_replace('\\', '/', $this->_getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    /**
     * Generate XML file
     *
     * @see http://www.sitemaps.org/protocol.html
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        $this->_initSitemapItems();
        /** @var $sitemapItem Varien_Object */
        foreach ($this->_sitemapItems as $sitemapItem) {
            $changefreq = $sitemapItem->getChangefreq();
            $priority = $sitemapItem->getPriority();
            foreach ($sitemapItem->getCollection() as $item) {
                $xml = $this->_getSitemapRow($item->getUrl(), $item->getUpdatedAt(), $changefreq, $priority);
                if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                    $this->_finalizeSitemap();
                }
                if (!$this->_fileSize) {
                    $this->_createSitemap();
                }
                $this->_writeSitemapRow($xml);
                // Increase counters
                $this->_lineCount++;
                $this->_fileSize += strlen($xml);
            }
        }
        $this->_finalizeSitemap();

        if ($this->_sitemapIncrement == 1) {
            // In case when only one increment file was created use it as default sitemap
            $this->_getFileHandler()
                ->mv($this->_getCurrentSitemapFilename($this->_sitemapIncrement), $this->getSitemapFilename());
        } else {
            // Otherwise create index file with list of generated sitemaps
            $this->_createSitemapIndex();
        }

        $this->addSitemapToRobotsTxt($this->getSitemapFilename());

        $this->setSitemapTime(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    protected function _createSitemapIndex()
    {
        $this->_createSitemap($this->getSitemapFilename(), self::TYPE_INDEX);
        for ($i = 1; $i <= $this->_sitemapIncrement; $i++) {
            $path = rtrim($this->getSitemapPath(), '/') . '/';
            $url =  $path . $this->_getCurrentSitemapFilename($i);
            $xml = $this->_getSitemapIndexRow($url, $this->_getCurrentDateTime());
            $this->_writeSitemapRow($xml);
        }
        $this->_finalizeSitemap(self::TYPE_INDEX);
    }

    /**
     * Get current date time
     *
     * @return string
     */
    protected function _getCurrentDateTime()
    {
        $date = new Varien_Date();
        return $date->now();
    }

    /**
     * Check is split required
     *
     * @param string $row
     * @return bool
     */
    protected function _isSplitRequired($row)
    {
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        $storeId = $this->getStoreId();
        if ($this->_lineCount + 1 > $helper->getMaximumLinesNumber($storeId)) {
            return true;
        }

        if ($this->_fileSize + strlen($row) > $helper->getMaximumFileSize($storeId)) {
            return true;
        }

        return false;
    }

    /**
     * Get sitemap row
     *
     * @param string $url
     * @param string $lastmod
     * @param string $changefreq
     * @param string $priority
     * @return string
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null)
    {
        $url = $this->_getUrl($url);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $changefreq . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $priority);
        }

        return '<url>' . $row . '</url>';
    }

    /**
     * Get sitemap index row
     *
     * @param string $url
     * @param string $lastmod
     * @return string
     */
    protected function _getSitemapIndexRow($url, $lastmod = null)
    {
        $url = $this->_getUrl($url);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $this->_getFormattedLastmodDate($lastmod) . '</lastmod>';
        }

        return '<sitemap>' . $row . '</sitemap>';
    }

    /**
     * Create new sitemap file
     *
     * @param string $fileName
     * @param string $type
     * @return void
     */
    protected function _createSitemap($fileName = null, $type = self::TYPE_URL)
    {
        if (!$fileName) {
            $this->_sitemapIncrement++;
            $fileName = $this->_getCurrentSitemapFilename($this->_sitemapIncrement);
        }
        $this->_fileHandler = $this->_getFileObject();
        $this->_fileHandler->setAllowCreateFolders(true);

        $path = $this->_fileHandler->getCleanPath($this->_getBaseDir() . $this->getSitemapPath());
        $this->_fileHandler->open(array('path' => $path));

        if ($this->_fileHandler->fileExists($fileName) && !$this->_fileHandler->isWriteable($fileName)) {
            Mage::throwException(Mage::helper('Mage_Sitemap_Helper_Data')
                ->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writable by web server.',
                    $fileName, $path
                )
            );
        }

        $fileHeader = sprintf($this->_tags[$type][self::OPEN_TAG_KEY], $type);
        $this->_fileHandler->streamOpen($fileName);
        $this->_fileHandler->streamWrite($fileHeader);

        $this->_fileSize = strlen($fileHeader . sprintf($this->_tags[$type][self::CLOSE_TAG_KEY], $type));
    }

    /**
     * Write sitemap row
     *
     * @param string $row
     */
    protected function _writeSitemapRow($row)
    {
        $this->_getFileHandler()->streamWrite($row . PHP_EOL);
    }

    /**
     * Write closing tag and close stream
     *
     * @param string $type
     */
    protected function _finalizeSitemap($type = self::TYPE_URL)
    {
        if ($this->_fileHandler) {
            $this->_fileHandler->streamWrite(sprintf($this->_tags[$type][self::CLOSE_TAG_KEY], $type));
            $this->_fileHandler->streamClose();
        }

        // Reset all counters
        $this->_lineCount = 0;
        $this->_fileSize = 0;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    protected function _getCurrentSitemapFilename($index)
    {
        return self::INDEX_FILE_PREFIX . '-' . $this->getStoreId() . '-' . $index . '.xml';
    }

    /**
     * Get base dir
     *
     * @return string
     */
    protected function _getBaseDir()
    {
        return Mage::getBaseDir();
    }

    /**
     * Get file object
     *
     * @return Varien_Io_File
     */
    protected function _getFileObject()
    {
        return new Varien_Io_File();
    }

    /**
     * Get store base url
     *
     * @param string $type
     * @return string
     */
    protected function _getStoreBaseUrl($type = Mage_Core_Model_Store::URL_TYPE_LINK)
    {
        return rtrim(Mage::app()->getStore($this->getStoreId())->getBaseUrl($type), '/') . '/';
    }

    /**
     * Get url
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    protected function _getUrl($url, $type = Mage_Core_Model_Store::URL_TYPE_LINK)
    {
        return $this->_getStoreBaseUrl($type) . ltrim($url, '/');
    }

    /**
     * Get media url
     *
     * @param string $url
     * @return string
     */
    protected function _getMediaUrl($url)
    {
        return $this->_getUrl($url, Mage_Core_Model_Store::URL_TYPE_MEDIA);
    }

    protected function _getFormattedLastmodDate($date)
    {
        return date('c', strtotime($date));
    }

    /**
     * Get base URL
     *
     * @return string
     */
    protected function _getBaseUrl()
    {
        return Mage::app()->getDefaultStoreView()->getBaseUrl();
    }

    /**
     * Get path to file robots.txt
     *
     * @return string
     */
    protected function _getRobotsTxtFilePath()
    {
        return $this->_getFileObject()->getCleanPath(Mage::getBaseDir() . '/robots.txt');
    }

    /**
     * Get domain from store base url
     *
     * @return string
     */
    protected function _getStoreBaseDomain()
    {
        $storeParsedUrl = parse_url($this->_getStoreBaseUrl());
        return $storeParsedUrl['scheme'] . '://' . $storeParsedUrl['host'];
    }

    /**
     * Get sitemap.xml URL according to all config options
     *
     * @param string $sitemapFileName
     * @return string
     */
    protected function _getSitemapUrl($sitemapFileName)
    {
        return $this->_getStoreBaseDomain() . str_replace('//', '/', $this->getSitemapPath() . '/' . $sitemapFileName);
    }

    /**
     * Add sitemap file to robots.txt
     *
     * @param string $sitemapFileName
     * @param string $replaceSitemapFileName
     */
    public function addSitemapToRobotsTxt($sitemapFileName, $replaceSitemapFileName = null)
    {
        $robotsSitemapLine = 'Sitemap: ' . $this->_getSitemapUrl($sitemapFileName);

        $robotsFileHandler = $this->_getFileObject();
        $robotsFileName = $this->_getRobotsTxtFilePath();
        $robotsFullText = '';
        if ($robotsFileHandler->fileExists($robotsFileName)) {
            $robotsFileHandler->open(array('path' => $robotsFileHandler->getDestinationFolder($robotsFileName)));
            $robotsFullText = $robotsFileHandler->read($robotsFileName);
        }

        $isReplacedFlag = false;
        if ($replaceSitemapFileName != null) {
            $regex = '{^sitemap:\s*?' . $replaceSitemapFileName . '}im';
            if (preg_match($regex, $robotsFullText)) {
                $robotsFullText = preg_replace($regex, $robotsSitemapLine, $robotsFullText, 1);
                $isReplacedFlag = true;
            }
        }

        if (!$isReplacedFlag) {
            if (strpos($robotsFullText, $robotsSitemapLine) === false) {
                if (!empty($robotsFullText)) {
                    $robotsFullText .= $this->_findNewLinesDelimiter($robotsFullText);
                }
                $robotsFullText .= $robotsSitemapLine;
            }
        }

        $robotsFileHandler->write($robotsFileName, $robotsFullText);
    }

    /**
     * Find new lines delimiter
     *
     * @param string $text
     * @return string
     */
    private function _findNewLinesDelimiter($text)
    {
        foreach ($this->_crlf as $delimiter) {
            if (strpos($text, $delimiter) !== false) {
                return $delimiter;
            }
        }

        return PHP_EOL;
    }

}
