<?php
/**
 * Users in role grid items updater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Role;

class UsersUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{
    /**
     * Filter name for users by role.
     */
    const IN_ROLE_USERS_PARAMETER = 'in_role_users';

    /**#@+
     * Supported values of filtering users by role.
     */
    const IN_ROLE_USERS_ANY = 1;
    const IN_ROLE_USERS_YES = 2;
    const IN_ROLE_USERS_NO = 3;
    /**#@-*/

    /**
     * @var int
     */
    protected $_roleId;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_inRoleUsersFilter;

    /**
     * Constructor.
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->_roleId = (int)$request->getParam('role_id');
        $this->_inRoleUsersFilter = $this->_parseInRoleUsersFilter($request, $backendHelper);
    }

    /**
     * Parse $_inRoleUsersFilter value from request
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @return int
     */
    protected function _parseInRoleUsersFilter(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $result = self::IN_ROLE_USERS_ANY;
        $filter = $backendHelper->prepareFilterString($request->getParam('filter', ''));
        if (isset($filter[self::IN_ROLE_USERS_PARAMETER])) {
            $result = $filter[self::IN_ROLE_USERS_PARAMETER] ? self::IN_ROLE_USERS_YES : self::IN_ROLE_USERS_NO;
        } elseif (!$request->isAjax()) {
            $result = self::IN_ROLE_USERS_YES;
        }
        return $result;
    }

    /**
     * Add filtering users by role.
     *
     * @param \Magento\Webapi\Model\Resource\Acl\User\Collection $collection
     * @return \Magento\Webapi\Model\Resource\Acl\User\Collection
     */
    public function update($collection)
    {
        if ($this->_roleId) {
            switch ($this->_inRoleUsersFilter) {
                case self::IN_ROLE_USERS_YES:
                    $collection->addFieldToFilter('role_id', $this->_roleId);
                    break;
                case self::IN_ROLE_USERS_NO:
                    $collection->addFieldToFilter('role_id', array(
                        array('neq' => $this->_roleId),
                        array('is' => new \Zend_Db_Expr('NULL'))
                    ));
                    break;
            }
        }
        return $collection;
    }
}
