<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Block\User\Edit\Tab;

class Roles extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        //$this->setDefaultFilter(array('assigned_user_role'=>1));
        $this->setTitle(__('User Roles Information'));
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->getSelectedRoles();
            if (empty($userRoles)) {
                $userRoles = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('role_id', array('in'=>$userRoles));
            } else {
                if ($userRoles) {
                    $this->getCollection()->addFieldToFilter('role_id', array('nin'=>$userRoles));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('\Magento\User\Model\Resource\Role\Collection');
        $collection->setRolesFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('assigned_user_role', array(
            'header_css_class' => 'a-center',
            'header'    => __('Assigned'),
            'type'      => 'radio',
            'html_name' => 'roles[]',
            'values'    => $this->getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'role_id'
        ));

        /*$this->addColumn('role_id', array(
            'header'    =>__('Role ID'),
            'index'     =>'role_id',
            'align'     => 'right',
            'width'    => '50px'
        ));*/

        $this->addColumn('role_name', array(
            'header'    =>__('Role'),
            'index'     =>'role_name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/rolesGrid', array('user_id' => \Mage::registry('permissions_user')->getUserId()));
    }

    public function getSelectedRoles($json=false)
    {
        if ( $this->getRequest()->getParam('user_roles') != "" ) {
            return $this->getRequest()->getParam('user_roles');
        }
        /* @var $user \Magento\User\Model\User */
        $user = \Mage::registry('permissions_user');
        //checking if we have this data and we
        //don't need load it through resource model
        if ($user->hasData('roles')) {
            $uRoles = $user->getData('roles');
        } else {
            $uRoles = $user->getRoles();
        }

        if ($json) {
            $jsonRoles = Array();
            foreach ($uRoles as $urid) {
                $jsonRoles[$urid] = 0;
            }
            return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode((object)$jsonRoles);
        } else {
            return $uRoles;
        }
    }

}
