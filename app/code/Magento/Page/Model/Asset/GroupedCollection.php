<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of page assets that combines into groups ones having the same properties
 */
namespace Magento\Page\Model\Asset;

class GroupedCollection extends \Magento\Core\Model\Page\Asset\Collection
{
    /**#@+
     * Special properties, enforced to be grouped by
     */
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_CAN_MERGE    = 'can_merge';
    /**#@-*/

    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var \Magento\Page\Model\Asset\PropertyGroup[]
     */
    private $_groups = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Add an instance, identified by a unique identifier, to the list and to the corresponding group
     *
     * @param string $identifier
     * @param \Magento\Core\Model\Page\Asset\AssetInterface $asset
     * @param array $properties
     */
    public function add($identifier, \Magento\Core\Model\Page\Asset\AssetInterface $asset, array $properties = array())
    {
        parent::add($identifier, $asset);
        $properties[self::PROPERTY_CONTENT_TYPE] = $asset->getContentType();
        $properties[self::PROPERTY_CAN_MERGE] = $asset instanceof \Magento\Core\Model\Page\Asset\MergeableInterface;
        $this->_getGroupFor($properties)->add($identifier, $asset);
    }

    /**
     * Retrieve existing or new group matching the properties
     *
     * @param array $properties
     * @return \Magento\Page\Model\Asset\PropertyGroup
     */
    private function _getGroupFor(array $properties)
    {
        /** @var $existingGroup \Magento\Page\Model\Asset\PropertyGroup */
        foreach ($this->_groups as $existingGroup) {
            if ($existingGroup->getProperties() == $properties) {
                return $existingGroup;
            }
        }
        /** @var $newGroup \Magento\Page\Model\Asset\PropertyGroup */
        $newGroup = $this->_objectManager->create(
            '\Magento\Page\Model\Asset\PropertyGroup', array('properties' => $properties)
        );
        $this->_groups[] = $newGroup;
        return $newGroup;
    }

    /**
     * Remove an instance from the list and from the corresponding group
     *
     * @param string $identifier
     */
    public function remove($identifier)
    {
        parent::remove($identifier);
        /** @var $group \Magento\Page\Model\Asset\PropertyGroup */
        foreach ($this->_groups as $group) {
            if ($group->has($identifier)) {
                $group->remove($identifier);
                return;
            }
        }
    }

    /**
     * Retrieve groups, containing assets that have the same properties
     *
     * @return \Magento\Page\Model\Asset\PropertyGroup[]
     */
    public function getGroups()
    {
        return $this->_groups;
    }
}
