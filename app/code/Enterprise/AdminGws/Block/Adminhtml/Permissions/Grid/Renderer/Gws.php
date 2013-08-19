<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website permissions column grid
 *
 */
class Enterprise_AdminGws_Block_Adminhtml_Permissions_Grid_Renderer_Gws extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var array
     */
    public static $websites = array();

    /**
     * Render cell contents
     *
     * Looks on the following data in the $row:
     * - is_all_permissions - bool
     * - website_ids - string, comma-separated
     * - store_group_ids - string, comma-separated
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        if ($row->getData('gws_is_all')) {
            return __('All');
        }

        // lookup websites and store groups in system
        if (!self::$websites) {
            foreach (Mage::getResourceSingleton('Mage_Core_Model_Resource_Store_Group_Collection') as $storeGroup) {
                /* @var $storeGroup Mage_Core_Model_Store_Group */
                $website = $storeGroup->getWebsite();
                $websiteId = (string)$storeGroup->getWebsiteId();
                self::$websites[$websiteId]['name'] = $website->getName();
                self::$websites[$websiteId][(int)$storeGroup->getId()] = $storeGroup->getName();
            }
        }

        // analyze current row values
        $storeGroupIds = array();
        if ($websiteIds = $row->getData('gws_websites')) {
            $websiteIds = explode(',', $websiteIds);
            foreach (self::$websites as $websiteId => $website) {
                if (in_array($websiteId, $websiteIds)) {
                    unset($website['name']);
                    $storeGroupIds = array_merge($storeGroupIds, array_keys($website));
                }
            }
        }
        else {
            $websiteIds = array();
            if ($ids = $row->getData('gws_store_groups')) {
                $storeGroupIds = explode(',', $ids);
            }
        }

        // walk through all websties and store groups and draw them
        $output = array();
        foreach (self::$websites as $websiteId => $website) {
            $isWebsite = in_array($websiteId, $websiteIds);
            // show only if something from this website is relevant
            if ($isWebsite || count(array_intersect(array_keys($website), $storeGroupIds))) {
                $output[] = $this->_formatName($website['name'], false, $isWebsite);
                foreach ($website as $storeGroupId => $storeGroupName) {
                    if (is_numeric($storeGroupId) && in_array($storeGroupId, $storeGroupIds)) {
                        $output[] = $this->_formatName($storeGroupName, true);
                    }
                }
            }
        }
        return $output ? implode('<br />', $output) : __('None');
    }

    /**
     * Format a name in cell
     *
     * @param string $name
     * @param bool $isStoreGroup
     * @param bool $isActive
     * @return string
     */
    protected function _formatName($name, $isStoreGroup = false, $isActive = true)
    {
        return '<span style="' . (!$isActive ? 'color:#999;text-decoration:line-through;' : '')
            . ($isStoreGroup ? 'padding-left:2em;' : '')
            . '">' . str_replace(' ', '&nbsp;', $name) . '</span>'
        ;
    }
}
