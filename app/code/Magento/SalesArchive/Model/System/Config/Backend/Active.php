<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_SalesArchive_Model_System_Config_Backend_Active
    extends Magento_Backend_Model_Config_Backend_Cache
    implements Magento_Backend_Model_Config_CommentInterface
{
    /**
     * @var Magento_SalesArchive_Model_ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var Magento_SalesArchive_Model_Resource_Order_Collection
     */
    protected $_orderCollection;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_SalesArchive_Model_ArchiveFactory $archiveFactory
     * @param Magento_SalesArchive_Model_Resource_Order_Collection $orderCollection
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_SalesArchive_Model_ArchiveFactory $archiveFactory,
        Magento_SalesArchive_Model_Resource_Order_Collection $orderCollection,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_archiveFactory = $archiveFactory;
        $this->_orderCollection = $orderCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        Magento_Backend_Block_Menu::CACHE_TAGS
    );

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged() && !$this->getValue()) {
            $this->_archiveFactory->create()->removeOrdersFromArchive();
        }
        return $this;
    }

    /**
     * Get field comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getCommentText($currentValue)
    {
        if ($currentValue) {
            $ordersCount = $this->_orderCollection->getSize();
            if ($ordersCount) {
                return __('There are %1 orders in this archive. All of them will be moved to the regular table after the archive is disabled.', $ordersCount);
            }
        }
        return '';
    }
}
