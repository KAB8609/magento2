<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Authorization model
 *
 * Checks if ACL Resource for Webapi Role is allowed, according to Webapi Access Control List
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization
{
    /**
     * ACL policy
     *
     * @var Magento_Authorization_Policy
     */
    protected $_aclPolicy;

    /**
     * ACL role locator
     *
     * @var Magento_Authorization_RoleLocator
     */
    protected $_aclRoleLocator;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_aclPolicy = isset($data['policy']) ? $data['policy'] : $this->_getAclPolicy();
        $this->_aclRoleLocator = isset($data['roleLocator']) ? $data['roleLocator'] : $this->_getAclRoleLocator();
    }

    /**
     * Get ACL policy object
     *
     * @return Magento_Authorization_Policy
     * @throws InvalidArgumentException
     */
    protected function _getAclPolicy()
    {
        $areaConfig = Mage::getConfig()->getAreaConfig();
        $policyClassName = isset($areaConfig['acl']['policy']) ?
            $areaConfig['acl']['policy'] :
            'Magento_Authorization_Policy_Default';

        /** @var $aclBuilder Mage_Core_Model_Acl_Builder */
        $aclBuilder = Mage::getSingleton('Mage_Core_Model_Acl_Builder', array(
            'areaConfig' => $areaConfig,
            'objectFactory' => Mage::getConfig(),
        ));

        /** @var $policyObject Magento_Authorization_Policy **/
        $policyObject = new $policyClassName($aclBuilder->getAcl());
        if (!($policyObject instanceof Magento_Authorization_Policy)) {
            throw new InvalidArgumentException($policyClassName . ' is not instance of Magento_Authorization_Policy');
        }

        return $policyObject;
    }

    /**
     * Get ACL role locator
     *
     * @return Magento_Authorization_RoleLocator
     * @throws InvalidArgumentException
     */
    protected function _getAclRoleLocator()
    {
        $areaConfig = Mage::getConfig()->getAreaConfig();
        $roleLocatorClassName = isset($areaConfig['acl']['roleLocator']) ?
            $areaConfig['acl']['roleLocator'] :
            'Magento_Authorization_RoleLocator_Default';

        /** @var $roleLocatorObject Magento_Authorization_RoleLocator **/
        $roleLocatorObject = Mage::getSingleton($roleLocatorClassName);

        if (!($roleLocatorObject instanceof Magento_Authorization_RoleLocator)) {
            throw new InvalidArgumentException(
                $roleLocatorClassName . ' is not instance of Magento_Authorization_RoleLocator'
            );
        }
        return $roleLocatorObject;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->_aclPolicy->isAllowed($this->_aclRoleLocator->getAclRoleId(), $resource, $privilege);
    }
}
