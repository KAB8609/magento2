<?php
/**
 * Options for Query Id column
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Adminhtml\Search\Grid;

class Options implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * \Magento\Search\Model\Resource\Recommendations
     */
    protected $_searchResourceModel;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Search\Model\Resource\Recommendations $searchResourceModel
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Registry $registry,
        \Magento\Search\Model\Resource\Recommendations $searchResourceModel
    ) {
        $this->_request = $request;
        $this->_registryManager = $registry;
        $this->_searchResourceModel = $searchResourceModel;
    }

    /**
     * Retrieve selected related queries from grid
     *
     * @return array
     */
    public function toOptionArray()
    {
        $queries = $this->_request->getPost('selected_queries');

        $currentQueryId = $this->_registryManager->registry('current_catalog_search')->getId();
        $queryIds = array();
        if (is_null($queries) && !empty($currentQueryId)) {
            $queryIds = $this->_searchResourceModel->getRelatedQueries($currentQueryId);
        }
        return $queryIds;
    }
}
