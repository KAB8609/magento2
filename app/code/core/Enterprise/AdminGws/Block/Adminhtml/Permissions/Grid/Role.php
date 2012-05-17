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
 * Admin roles grid
 *
 */
class Enterprise_AdminGws_Block_Adminhtml_Permissions_Grid_Role extends Mage_User_Block_Role_Grid
{
    /**
     * Add allowed websites/stores column
     *
     * @return Enterprise_Permissions_Block_Permissions_Grid_Role
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('gws', array(
            'header'   => $this->__('Allowed Scopes'),
            'width'    => '200',
            'sortable' => false,
            'filter'   => false,
            'renderer' => 'Enterprise_AdminGws_Block_Adminhtml_Permissions_Grid_Renderer_Gws'
        ));

        return $this;
    }
}
