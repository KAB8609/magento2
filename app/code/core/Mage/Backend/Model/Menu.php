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
 * Backend menu model
 */
class Mage_Backend_Model_Menu extends ArrayObject
{
    /**
     * Path in tree structure
     *
     * @var string
     */
    protected $_path = '';

    /**
     * @var Mage_Backend_Model_Menu_Logger
     */
    protected $_logger;

    /**
     * @param array $array
     */
    public function __construct(Mage_Core_Model_Logger $logger, $path = '')
    {
        if ($path) {
            $this->_path = $path . '/';
        }
        $this->_logger = $logger;
        $this->setIteratorClass('Mage_Backend_Model_Menu_Iterator');
    }

    /**
     * Add child to menu item
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @param string $parentId
     * @param int $index
     * @throws InvalidArgumentException
     */
    public function add(Mage_Backend_Model_Menu_Item $item, $parentId = null, $index = null)
    {
        if (!is_null($parentId)) {
            $parentItem = $this->get($parentId);
            if ($parentItem === null) {
                throw new InvalidArgumentException("Item with identifier {$parentId} does not exist");
            }
            $parentItem->getChildren()->add($item, null, $index);
        } else {
            $index = intval($index);
            if (!isset($this[$index])) {
                $this->offsetSet($index, $item);
                $this->_logger->log(sprintf('Add of item with id %s was processed', $item->getId()));
            } else {
                $this->add($item, $parentId, $index + 1);
            }
        }
    }

    /**
     * Retrieve menu item by id
     *
     * @param string $itemId
     * @return Mage_Backend_Model_Menu_Item|null
     */
    public function get($itemId)
    {
        $result = null;
        foreach ($this as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                $result = $item;
                break;
            }

            if ($item->hasChildren() && ($result = $item->getChildren()->get($itemId))) {
                break;
            }
        }
        return $result;
    }

    /**
     * Move menu item
     *
     * @param string $itemId
     * @param string $toItemId
     * @param int $sortIndex
     */
    public function move($itemId, $toItemId, $sortIndex = null)
    {
        $item = $this->get($itemId);
        if ($item === null) {
            throw new InvalidArgumentException("Item with identifier {$itemId} does not exist");
        }
        $this->remove($itemId);
        $this->add($item, $toItemId, $sortIndex);
    }

    /**
     * Remove menu item by id
     *
     * @param string $itemId
     * @return bool
     */
    public function remove($itemId)
    {
        $result = false;
        foreach ($this as $key => $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $result = true;
                $this->_logger->log(sprintf('Remove on item with id %s was processed', $item->getId()));
                break;
            }

            if ($item->hasChildren() && ($result = $item->getChildren()->remove($itemId))) {
                break;
            }
        }
        return $result;
    }

    /**
     * Change order of an item in its parent menu
     *
     * @param string $itemId
     * @param int $position
     * @return bool
     */
    public function reorder($itemId, $position)
    {
        $result = false;
        foreach ($this as $key => $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $this->add($item, null, $position);
                $result = true;
                break;
            } else if ($item->hasChildren() && $result = $item->getChildren()->reorder($itemId, $position)) {
                break;
            }
        }
        return $result;
    }

    /**
     * Check whether provided item is last in list
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return bool
     */
    public function isLast(Mage_Backend_Model_Menu_Item $item)
    {
        return $this->offsetGet(max(array_keys($this->getArrayCopy())))->getId() == $item->getId();
    }

    /**
     * Find first menu item that user is able to access
     *
     * @return Mage_Backend_Model_Menu_Item|null
     */
    public function getFirstAvailable()
    {
        $result = null;
        /** @var $item Mage_Backend_Model_Menu_Item */
        foreach ($this as $item) {
            if ($item->isAllowed() && !$item->isDisabled()) {
                if ($item->hasChildren()) {
                    $result = $item->getChildren()->getFirstAvailable();
                    if (false == is_null($result)) {
                        break;
                    }
                } else {
                    $result = $item;
                    break;
                }
            }
        }
        return $result;
    }
}
