<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index backend controller
 */
namespace Magento\Backend\Controller\Adminhtml;

class Index extends \Magento\Backend\Controller\AbstractAction
{
    /**
     * Global Search Action
     */
    public function globalSearchAction()
    {
        $searchModules = $this->_objectManager->get('Magento\Core\Model\Config')->getNode("adminhtml/global_search");
        $items = array();

        if (!$this->_authorization->isAllowed('Magento_Adminhtml::global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => __('Error'),
                'name' => __('Access Denied'),
                'description' => __('You need more permissions to do this.')
            );
        } else {
            if (empty($searchModules)) {
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
                foreach ($searchModules->children() as $searchConfig) {

                    if ($searchConfig->acl && !$this->_authorization->isAllowed($searchConfig->acl)){
                        continue;
                    }

                    $className = $searchConfig->getClassName();

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

    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }
}
