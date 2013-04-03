<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Launcher page tile collection
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Resource_Tile_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Tile factory
     *
     * @var Saas_Launcher_Model_TileFactory
     */
    protected $_tileFactory;

    /**
     * Class constructor
     *
     * @param Saas_Launcher_Model_TileFactory $tileFactory
     * @param Mage_Core_Model_Resource_Db_Abstract|null $resource
     */
    public function __construct(
        Saas_Launcher_Model_TileFactory $tileFactory,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($resource);
        $this->_tileFactory = $tileFactory;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Saas_Launcher_Model_Tile', 'Saas_Launcher_Model_Resource_Tile');
    }

    /**
     * Retrieve new tile instance
     *
     * @return Saas_Launcher_Model_Tile
     */
    public function getNewEmptyItem()
    {
        return $this->_tileFactory->create();
    }

    /**
     * Redeclare after load method for specifying collection items StateResolvers and SaveHandlers
     *
     * @return Saas_Launcher_Model_Resource_Tile_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->getItems() as $item) {
            $this->_tileFactory->setStateResolverAndSaveHandler($item);
        }
        return $this;
    }
}
