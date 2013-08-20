<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User column filter for Event Log grid
 */
class Enterprise_Logging_Block_Adminhtml_Grid_Filter_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * Build filter options list
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = array(array('value' => '', 'label' => __('All Users')));
        foreach (Mage::getResourceModel('Enterprise_Logging_Model_Resource_Event')->getUserNames() as $username) {
            $options[] = array('value' => $username, 'label' => $username);
        }
        return $options;
    }

    /**
     * Filter condition getter
     *
     * @string
     */
    public function getCondition()
    {
        return $this->getValue();
    }
}
