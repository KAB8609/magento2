<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * List of page assets that combines into groups ones having the same properties
 */
class GroupedCollection extends Collection
{
    /**#@+
     * Special properties, enforced to be grouped by
     */
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_CAN_MERGE    = 'can_merge';
    /**#@-*/

    /**
     * @var \Magento\View\Asset\PropertyGroupFactory
     */
    protected $propertyFactory;

    /**
     * @var PropertyGroup[]
     */
    protected $groups = array();

    /**
     * @param PropertyGroupFactory $propertyFactory
     */
    public function __construct(PropertyGroupFactory $propertyFactory)
    {
        $this->propertyFactory = $propertyFactory;
    }

    /**
     * Add an instance, identified by a unique identifier, to the list and to the corresponding group
     *
     * @param string $identifier
     * @param AssetInterface $asset
     * @param array $properties
     */
    public function add($identifier, AssetInterface $asset, array $properties = array())
    {
        parent::add($identifier, $asset);
        $properties[self::PROPERTY_CONTENT_TYPE] = $asset->getContentType();
        $properties[self::PROPERTY_CAN_MERGE] = $asset instanceof MergeableInterface;
        $this->getGroupFor($properties)->add($identifier, $asset);
    }

    /**
     * Retrieve existing or new group matching the properties
     *
     * @param array $properties
     * @return PropertyGroup
     */
    private function getGroupFor(array $properties)
    {
        /** @var $existingGroup PropertyGroup */
        foreach ($this->groups as $existingGroup) {
            if ($existingGroup->getProperties() == $properties) {
                return $existingGroup;
            }
        }
        /** @var $newGroup PropertyGroup */
        $newGroup = $this->propertyFactory->create(array('properties' => $properties));
        $this->groups[] = $newGroup;
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
        /** @var $group PropertyGroup */
        foreach ($this->groups as $group) {
            if ($group->has($identifier)) {
                $group->remove($identifier);
                return;
            }
        }
    }

    /**
     * Retrieve groups, containing assets that have the same properties
     *
     * @return PropertyGroup[]
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
