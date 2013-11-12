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
 * Adminhtml newsletter subscribers controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Newsletter;

class Subscriber extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_title->add(__('Newsletter Subscribers'));

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('Magento_Newsletter::newsletter_subscriber');

        $this->_addBreadcrumb(__('Newsletter'), __('Newsletter'));
        $this->_addBreadcrumb(__('Subscribers'), __('Subscribers'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
     }

    /**
     * Export subscribers grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'subscribers.csv';
        $content = $this->getLayout()->getChildBlock('adminhtml.newslettrer.subscriber.grid', 'grid.export');

        return $this->_fileFactory->create($fileName, $content->getCsvFile($fileName));
    }

    /**
     * Export subscribers grid to XML format
     */
    public function exportXmlAction()
    {
        $this->loadLayout();
        $fileName = 'subscribers.xml';
        $content = $this->getLayout()->getChildBlock('adminhtml.newslettrer.subscriber.grid', 'grid.export');
        return $this->_fileFactory->create($fileName, $content->getExcelFile($fileName));
    }

    public function massUnsubscribeAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Please select one or more subscribers.'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = $this->_objectManager->create('Magento\Newsletter\Model\Subscriber')->load($subscriberId);
                    $subscriber->unsubscribe();
                }
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('A total of %1 record(s) were updated.', count($subscribersIds))
                );
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/*/index');
    }

    public function massDeleteAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Please select one or more subscribers.'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = $this->_objectManager->create('Magento\Newsletter\Model\Subscriber')->load($subscriberId);
                    $subscriber->delete();
                }
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('Total of %1 record(s) were deleted', count($subscribersIds))
                );
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Newsletter::subscriber');
    }
}
