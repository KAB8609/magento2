<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Model_Tabs
{
    /**
     * Store enabled application design tabs
     *
     * @var array
     */
    protected $_enabledTabs = array();

    /**
     * Store disabled application design tabs
     *
     * @var array
     */
    protected $_disabledTabs = array();

    /**
     * Set enabled and disabled application tabs
     */
    public function __construct($data)
    {
        $this->_enabledTabs = Mage::helper('xmlconnect')->getDefaultApplicationDesignTabs();

        if (is_string($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $this->_enabledTabs = $data->enabledTabs;
                $this->_disabledTabs = $data->disabledTabs;
            }
        }
    }

    /**
     * Getter for enabled tabs
     */
    public function getEnabledTabs()
    {
        return $this->_enabledTabs;
    }

    /**
     * Getter for disabled tabs
     */
    public function getDisabledTabs()
    {
        return $this->_disabledTabs;
    }

    /**
     * Collect tabs with images
     */
    public function getRenderTabs()
    {
        $result = array();
        foreach ($this->_enabledTabs as $tab) {
            $tab->image = Mage::getDesign()->getSkinUrl('images/xmlconnect/' . $tab->image);
            $result[] = $tab;
        }
        return $result;
    }
}
