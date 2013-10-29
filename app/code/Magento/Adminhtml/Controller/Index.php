<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index admin controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated  Partially moved to module Backend
 */
namespace Magento\Adminhtml\Controller;

class Index extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Search modules list
     *
     * @var array
     */
    protected $_searchModules;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param array $searchModules
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        array $searchModules = array()
    ) {
        $this->_searchModules = $searchModules;
        parent::__construct($context);
    }

    /**
     * Global Search Action
     */
    public function globalSearchAction()
    {
        $items = array();
        
        if (!$this->_authorization->isAllowed('Magento_Adminhtml::global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => __('Error'),
                'name' => __('Access Denied'),
                'description' => __('You need more permissions to do this.')
            );
        } else {
            if (empty($this->_searchModules)) {
                $items[] = array(
                    'id' => 'error',
                    'type' => __('Error'),
                    'name' => __('No search modules were registered'),
                    'description' => __('Please make sure that all global admin search modules are installed and activated.')
                );
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                foreach ($this->_searchModules as $searchConfig) {

                    if ($searchConfig['acl'] && !$this->_authorization->isAllowed($searchConfig['acl'])){
                        continue;
                    }

                    $className = $searchConfig['class'];
                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = $this->_objectManager->create($className);
                    $results = $searchInstance->setStart($start)
                        ->setLimit($limit)
                        ->setQuery($query)
                        ->load()
                        ->getResults();
                    $items = array_merge_recursive($items, $results);
                }
            }
        }

        $this->getResponse()->setBody(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($items)
        );
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
