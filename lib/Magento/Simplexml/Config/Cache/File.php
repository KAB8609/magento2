<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Simplexml
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * File based cache for configuration
 *
 * @category   Magento
 * @package    Magento_Simplexml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Simplexml_Config_Cache_File extends Magento_Simplexml_Config_Cache_Abstract
{

    /**
     * Initialize variables that depend on the cache key
     *
     * @param string $key
     * @return string
     */
    public function setKey($key)
    {
    	$this->setData('key', $key);
    	
    	$file = $this->getDir().DS.$this->getKey();
        $this->setFileName($file.'.xml');
        $this->setStatFileName($file.'.stat');
        
        return $this;
    }

    /**
     * Try to load configuration cache from file
     *
     * @return boolean
     */
    public function load()
    {
        $this->setIsLoaded(false);

        // try to read stats
        if (!($stats = @file_get_contents($this->getStatFileName()))) {
            return false;
        }

        // try to validate stats
        if (!$this->validateComponents(unserialize($stats))) {
            return false;
        }
        
        // try to read cache file
        if (!($cache = @file_get_contents($this->getFileName()))) {
            return false;
        }
            
        // try to process cache file
        if (!($data = $this->getConfig()->processFileData($cache))) {
            return false;
        }

        $xml = $this->getConfig()->loadString($data);
        $this->getConfig()->setXml($xml);
        $this->setIsLoaded(true);

        return true;
    }
    
    /**
     * Try to save configuration cache to file
     *
     * @return boolean
     */
    public function save()
    {
        if (!$this->getIsAllowedToSave()) {
            return false;
        }
        
        // save stats
        @file_put_contents($this->getStatFileName(), serialize($this->getComponents()));

        // save cache
        @file_put_contents($this->getFileName(), $this->getConfig()->getNode()->asNiceXml());
        
        return true;
    }
}