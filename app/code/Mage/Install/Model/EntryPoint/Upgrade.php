<?php
/**
 * Entry point for upgrading application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_Upgrade extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Key for passing reindexing parameter
     */
    const REINDEX = 'reindex';

    /**@#+
     * Reindexing modes
     */
    const REINDEX_INVALID = 1;
    const REINDEX_ALL = 2;
    /**@#-*/

    /**
     * Apply scheme & data updates
     */
    public function processRequest()
    {
        /** @var $cacheFrontendPool Mage_Core_Model_Cache_Frontend_Pool */
        $cacheFrontendPool = $this->_objectManager->get('Mage_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }

        /** @var $updater \Mage_Core_Model_Db_Updater */
        $updater = $this->_objectManager->get('Mage_Core_Model_Db_Updater');
        $updater->updateScheme();
        $updater->updateData();

        $this->_reindex();
    }

    /**
     * Perform reindexing if requested
     */
    private function _reindex()
    {
        /** @var $config Mage_Core_Model_Config_Primary */
        $config = $this->_objectManager->get('Mage_Core_Model_Config_Primary');
        $reindexMode = $config->getParam(self::REINDEX);
        if ($reindexMode) {
            /** @var $indexer Mage_Index_Model_Indexer */
            $indexer = $this->_objectManager->get('Mage_Index_Model_Indexer');
            if (self::REINDEX_ALL == $reindexMode) {
                $indexer->reindexAll();
            } elseif (self::REINDEX_INVALID == $reindexMode) {
                $indexer->reindexRequired();
            }
        }
    }
}
