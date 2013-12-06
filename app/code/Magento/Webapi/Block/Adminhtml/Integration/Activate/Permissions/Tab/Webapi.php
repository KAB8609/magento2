<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\Integration\Activate\Permissions\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\View\Element\Template;
use Magento\Acl\Resource\ProviderInterface;
use Magento\Core\Helper\Data as CoreHelper;
use Magento\Core\Model\Acl\RootResource;
use Magento\View\Element\Template\Context;
use Magento\Integration\Helper\Data as IntegrationHelper;
use Magento\Webapi\Helper\Data as WebapiHelper;

/**
 * API permissions tab for integration activation dialog.
 */
class Webapi extends Template implements TabInterface
{
    /** @var string[] */
    protected $_selectedResources;

    /** @var RootResource */
    protected $_rootResource;

    /** @var ProviderInterface */
    protected $_resourceProvider;

    /** @var IntegrationHelper */
    protected $_integrationData;

    /** @var WebapiHelper */
    protected $_webapiHelper;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param RootResource $rootResource
     * @param ProviderInterface $resourceProvider
     * @param IntegrationHelper $integrationData
     * @param WebapiHelper $webapiData
     * @param array $data
     */
    public function __construct(
        Context $context,
        RootResource $rootResource,
        ProviderInterface $resourceProvider,
        IntegrationHelper $integrationData,
        WebapiHelper $webapiData,
        array $data = array()
    ) {
        $this->_rootResource = $rootResource;
        $this->_webapiHelper = $webapiData;
        $this->_resourceProvider = $resourceProvider;
        $this->_integrationData = $integrationData;
        parent::__construct($context, $data);
    }

    /**
     * Set the selected resources, which is an array of resource ids. If everything is allowed, the
     * array will contain just the root resource id, which is "Magento_Adminhtml::all".
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_selectedResources = $this->_webapiHelper->getSelectedResources();
    }

    /**
     * {@inheritDoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getTabLabel()
    {
        return __('API');
    }

    /**
     * {@inheritDoc}
     */
    public function getTabTitle()
    {
        return __('API');
    }

    /**
     * {@inheritDoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check if everything is allowed.
     *
     * @return bool
     */
    public function isEverythingAllowed()
    {
        return in_array($this->_rootResource->getId(), $this->_selectedResources);
    }

    /**
     * Get requested permissions tree.
     *
     * @return string
     */
    public function getResourcesTreeJson()
    {
        $resources = $this->_resourceProvider->getAclResources();
        $aclResourcesTree = $this->_integrationData->mapResources($resources[1]['children']);

        return $this->_coreData->jsonEncode($aclResourcesTree);
    }

    /**
     * Return an array of selected resource ids. If everything is allowed then iterate through all
     * available resources to generate a comprehensive array of all resource ids, rather than just
     * returning "Magento_Adminhtml::all".
     *
     * @return string
     */
    public function getSelectedResourcesJson()
    {
        $selectedResources = $this->_selectedResources;
        if ($this->isEverythingAllowed()) {
             $resources = $this->_resourceProvider->getAclResources();
             $selectedResources = $this->_getAllResourceIds($resources[1]['children']);
        }
        return $this->_coreData->jsonEncode($selectedResources);
    }

    /**
     * Return an array of all resource Ids.
     *
     * @param array $resources
     * @return string[]
     */
    protected function _getAllResourceIds(array $resources)
    {
        $resourceIds = array();
        foreach ($resources as $resource) {
            $resourceIds[] = $resource['id'];
            if (isset($resource['children'])) {
                $resourceIds = array_merge($resourceIds, $this->_getAllResourceIds($resource['children']));
            }
        }
        return $resourceIds;
    }
}
