<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Webapi_Model_Acl_Resource_Provider extends  Magento_Acl_Resource_Provider
    implements Mage_Webapi_Model_Acl_Resource_ProviderInterface
{
    /**
     * @param Mage_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader
     * @param Magento_Config_ScopeInterface $scope
     * @param Magento_Acl_Resource_TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        Mage_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader,
        Magento_Config_ScopeInterface $scope,
        Magento_Acl_Resource_TreeBuilder $resourceTreeBuilder
    ) {
        parent::__construct($configReader, $scope, $resourceTreeBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getAclVirtualResources()
    {
        $aclResourceConfig = $this->_configReader->read($this->_scope->getCurrentScope());
        return $aclResourceConfig['config']['mapping'];
    }
}
