<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Acl\Builder as AclBuilder;
use Magento\Acl;
use Magento\Authz\Model\UserIdentifier;
use Magento\Logger;
use Magento\Service\Exception as ServiceException;
use Magento\Service\ResourceNotFoundException;
use Magento\User\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;
use Magento\User\Model\Role;
use Magento\User\Model\RoleFactory;
use Magento\User\Model\RulesFactory;

/**
 * Authorization service.
 *
 * TODO: Fix code and remove warnings suppression
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizationV1 implements AuthorizationV1Interface
{
    /** @var AclBuilder */
    protected $_aclBuilder;

    /** @var UserIdentifier */
    protected $_userIdentifier;

    /** @var RoleFactory */
    protected $_roleFactory;

    /** @var RoleCollectionFactory */
    protected $_roleCollectionFactory;

    /** @var RulesFactory */
    protected $_rulesFactory;

    /** @var Logger */
    protected $_logger;

    /**
     * @param AclBuilder $aclBuilder
     * @param UserIdentifier $userIdentifier
     * @param RoleFactory $roleFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param RulesFactory $rulesFactory
     * @param Logger $logger
     */
    public function __construct(
        AclBuilder $aclBuilder,
        UserIdentifier $userIdentifier,
        RoleFactory $roleFactory,
        RoleCollectionFactory $roleCollectionFactory,
        RulesFactory $rulesFactory,
        Logger $logger
    ) {
        $this->_aclBuilder = $aclBuilder;
        $this->_userIdentifier = $userIdentifier;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($resources, $userIdentifier = null)
    {
        $resources = is_array($resources) ? $resources : array($resources);
        $userIdentifier = $userIdentifier ? $userIdentifier : $this->_userIdentifier;
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                throw new ResourceNotFoundException(
                    __(
                        'Role for user with ID "%1" and user type "%2" cannot be found.',
                        $userIdentifier->getUserId(),
                        $userIdentifier->getUserType()
                    )
                );
            }
            foreach ($resources as $resource) {
                // TODO: Currently ACL files are located under adminhtml (except several), but should be made global
                if (!$this->_aclBuilder->getAcl()->isAllowed($role->getId(), $resource)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function grantPermissions($userIdentifier, $resources)
    {
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                $role = $this->_createRole($userIdentifier);
            }
            $this->_associateResourcesWithRole($role, $resources);
        } catch (ServiceException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->log($e);
            throw new ServiceException(
                __('Error happened while granting permissions. Check exception log for details.')
            );
        }
    }

    /**
     * Create new ACL role.
     *
     * @param UserIdentifier $userIdentifier
     * @return Role
     * @throws \LogicException
     */
    protected function _createRole($userIdentifier)
    {
        $userType = $userIdentifier->getUserType();
        $userId = $userIdentifier->getUserId();
        switch ($userType) {
            case UserIdentifier::USER_TYPE_ADMIN:
                // TODO: Should be implemented if current approach is accepted
                throw new \Exception("Not implemented yet.");
                break;
            case UserIdentifier::USER_TYPE_INTEGRATION:
                $roleName = $userType . $userId;
                $roleType = \Magento\User\Model\Acl\Role\User::ROLE_TYPE;
                $parentId = 0;
                $userId = $userIdentifier->getUserId();
                break;
            case UserIdentifier::USER_TYPE_CUSTOMER:
                /** Break is intentionally omitted. */
            case UserIdentifier::USER_TYPE_GUEST:
                $roleName = $userType;
                $roleType = \Magento\User\Model\Acl\Role\User::ROLE_TYPE;
                $parentId = 0;
                $userId = 0;
                if ($this->_getUserRole($userIdentifier)) {
                    throw new \LogicException(
                        "There should be not more than one role for '{$userType}' user type."
                    );
                }
                break;
            default:
                throw new \LogicException("Unknown user type: '{$userType}'.");
        }
        $role = $this->_roleFactory->create();
        $role->setRoleName($roleName)
            ->setUserType($userType)
            ->setUserId($userId)
            ->setRoleType($roleType)
            ->setParentId($parentId)
            ->save();
        return $role;
    }

    /**
     * Identify user role from user identifier.
     *
     * @param UserIdentifier $userIdentifier
     * @return Role|false Return false in case when no role associated with provided user was found.
     */
    protected function _getUserRole($userIdentifier)
    {
        $roleCollection = $this->_roleCollectionFactory->create();
        /** @var Role $role */
        $userType = $userIdentifier->getUserType();
        /** User ID does not matter for customer permissions check as there is a single customer role. */
        $userId = ($userType == UserIdentifier::USER_TYPE_CUSTOMER) ? 0 : $userIdentifier->getUserId();
        $role = $roleCollection->setUserFilter($userId, $userType)->getFirstItem();
        return $role->getId() ? $role : false;
    }

    /**
     * Associate resources with the specified role. All resources previously assigned to the role will be unassigned.
     *
     * @param Role $role
     * @param string[] $resources
     */
    protected function _associateResourcesWithRole($role, $resources)
    {
        /** @var \Magento\User\Model\Rules $rules */
        $rules = $this->_rulesFactory->create();
        $rules->setRoleId($role->getId())->setResources($resources)->saveRel();
    }
}
