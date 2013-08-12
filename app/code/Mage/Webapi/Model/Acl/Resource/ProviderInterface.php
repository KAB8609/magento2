<?php
/**
 * Web API ACL resources provider interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Webapi_Model_Acl_Resource_ProviderInterface extends Magento_Acl_Resource_ProviderInterface
{
    /**
     * Retrieve ACL Virtual Resources.
     *
     * Virtual resources are not shown in resource list, they use existing resource to check permission.
     *
     * @return array
     */
    public function getAclVirtualResources();
}

