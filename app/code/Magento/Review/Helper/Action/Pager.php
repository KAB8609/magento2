<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Action pager helper for iterating over search results
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Helper\Action;

class Pager extends \Magento\App\Helper\AbstractHelper
{
    const STORAGE_PREFIX = 'search_result_ids';

    /**
     * @var int
     */
    protected $_storageId = null;

    /**
     * @var array
     */
    protected $_items = null;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * Set storage id
     *
     * @param $storageId
     */
    public function setStorageId($storageId)
    {
        $this->_storageId = $storageId;
    }

    /**
     * Set items to storage
     *
     * @param array $items
     * @return \Magento\Review\Helper\Action\Pager
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
        $this->_backendSession->setData($this->_getStorageKey(), $this->_items);

        return $this;
    }

    /**
     * Load stored items
     */
    protected function _loadItems()
    {
        if (is_null($this->_items)) {
            $this->_items = (array) $this->_backendSession->getData($this->_getStorageKey());
        }
    }

    /**
     * Get next item id
     *
     * @param int $id
     * @return int|bool
     */
    public function getNextItemId($id)
    {
        $position = $this->_findItemPositionByValue($id);
        if ($position === false || $position == count($this->_items) - 1) {
            return false;
        }

        return $this->_items[$position + 1];
    }

    /**
     * Get previous item id
     *
     * @param int $id
     * @return int|bool
     */
    public function getPreviousItemId($id)
    {
        $position = $this->_findItemPositionByValue($id);
        if ($position === false || $position == 0) {
            return false;
        }

        return $this->_items[$position - 1];
    }

    /**
     *
     *
     * @param mixed $value
     * @return int|bool
     */
    protected function _findItemPositionByValue($value)
    {
        $this->_loadItems();
        return array_search($value, $this->_items);
    }

    /**
     * Get storage key
     *
     * @return string
     */
    protected function _getStorageKey()
    {
        if (!$this->_storageId) {
            throw new \Magento\Core\Exception(__('Storage key was not set'));
        }

        return self::STORAGE_PREFIX . $this->_storageId;
    }
}
