<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Controller_Adminhtml_Process extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize process object by request
     *
     * @return Magento_Index_Model_Process|false
     */
    protected function _initProcess()
    {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            /** @var $process Magento_Index_Model_Process */
            $process = Mage::getModel('Magento_Index_Model_Process')->load($processId);
            if ($process->getId() && $process->getIndexer()->isVisible()) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Display processes grid action
     */
    public function listAction()
    {
        $this->_title(__('Index Management'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Index::system_index');
        $this->_addContent($this->getLayout()->createBlock('Magento_Index_Block_Adminhtml_Process'));
        $this->renderLayout();
    }

    /**
     * Process detail and edit action
     */
    public function editAction()
    {
        /** @var $process Magento_Index_Model_Process */
        $process = $this->_initProcess();
        if ($process) {
            $this->_title($process->getIndexCode());

            $this->_title(__('System'))
                 ->_title(__('Index Management'))
                 ->_title(__($process->getIndexer()->getName()));

            Mage::register('current_index_process', $process);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(
                __('Cannot initialize the indexer process.')
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Save process data
     */
    public function saveAction()
    {
        /** @var $process Magento_Index_Model_Process */
        $process = $this->_initProcess();
        if ($process) {
            $mode = $this->getRequest()->getPost('mode');
            if ($mode) {
                $process->setMode($mode);
            }
            try {
                $process->save();
                $this->_getSession()->addSuccess(
                    __('The index has been saved.')
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     __('There was a problem with saving process.')
                );
            }
            $this->_redirect('*/*/list');
        } else {
            $this->_getSession()->addError(
                __('Cannot initialize the indexer process.')
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Reindex all data what process is responsible
     */
    public function reindexProcessAction()
    {
        /** @var $process Magento_Index_Model_Process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                Magento_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                Magento_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->_getSession()->addSuccess(
                    __('%1 index was rebuilt.', $process->getIndexer()->getName())
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     __('There was a problem with reindexing process.')
                );
            }
        } else {
            $this->_getSession()->addError(
                __('Cannot initialize the indexer process.')
            );
        }

        $this->_redirect('*/*/list');
    }

    /**
     * Reindex pending events for index process
     */
    public function reindexEventsAction()
    {

    }

    /**
     * Rebiuld all processes index
     */
    public function reindexAllAction()
    {

    }

    /**
     * Mass rebuild selected processes index
     *
     */
    public function massReindexAction()
    {
        /* @var $indexer Magento_Index_Model_Indexer */
        $indexer    = Mage::getSingleton('Magento_Index_Model_Indexer');
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_getSession()->addError(__('Please select Indexes'));
        } else {
            try {
                $counter = 0;
                foreach ($processIds as $processId) {
                    /* @var $process Magento_Index_Model_Process */
                    $process = $indexer->getProcessById($processId);
                    if ($process && $process->getIndexer()->isVisible()) {
                        $process->reindexEverything();
                        $counter++;
                    }
                }
                $this->_getSession()->addSuccess(
                    __('Total of %1 index(es) have reindexed data.', $counter)
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, __('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('*/*/list');
    }

    /**
     * Mass change index mode of selected processes index
     *
     */
    public function massChangeModeAction()
    {
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_getSession()->addError(__('Please select Index(es)'));
        } else {
            try {
                $counter = 0;
                $mode = $this->getRequest()->getParam('index_mode');
                foreach ($processIds as $processId) {
                    /* @var $process Magento_Index_Model_Process */
                    $process = Mage::getModel('Magento_Index_Model_Process')->load($processId);
                    if ($process->getId() && $process->getIndexer()->isVisible()) {
                        $process->setMode($mode)->save();
                        $counter++;
                    }
                }
                $this->_getSession()->addSuccess(
                    __('Total of %1 index(es) have changed index mode.', $counter)
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, __('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('*/*/list');
    }

    /**
     * Check ACL permissins
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Index::index');
    }
}