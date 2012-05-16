<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Index_Adminhtml_ProcessController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize process object by request
     *
     * @return Mage_Index_Model_Process|false
     */
    protected function _initProcess()
    {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            $process = Mage::getModel('Mage_Index_Model_Process')->load($processId);
            if ($process->getId()) {
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
        $this->_title($this->__('System'))->_title($this->__('Index Management'));

        $this->loadLayout();
        $this->_setActiveMenu('system/index');
        $this->_addContent($this->getLayout()->createBlock('Mage_Index_Block_Adminhtml_Process'));
        $this->renderLayout();
    }

    /**
     * Process detail and edit action
     */
    public function editAction()
    {
        $process = $this->_initProcess();
        if ($process) {
            $this->_title($process->getIndexCode());

            $this->_title($this->__('System'))
                 ->_title($this->__('Index Management'))
                 ->_title($this->__($process->getIndexer()->getName()));

            Mage::register('current_index_process', $process);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(
                Mage::helper('Mage_Index_Helper_Data')->__('Cannot initialize the indexer process.')
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Save process data
     */
    public function saveAction()
    {
        $process = $this->_initProcess();
        if ($process) {
            $mode = $this->getRequest()->getPost('mode');
            if ($mode) {
                $process->setMode($mode);
            }
            try {
                $process->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Index_Helper_Data')->__('The index has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     Mage::helper('Mage_Index_Helper_Data')->__('There was a problem with saving process.')
                );
            }
            $this->_redirect('*/*/list');
        } else {
            $this->_getSession()->addError(
                Mage::helper('Mage_Index_Helper_Data')->__('Cannot initialize the indexer process.')
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Reindex all data what process is responsible
     */
    public function reindexProcessAction()
    {
        $process = $this->_initProcess();
        if ($process) {
            try {
                Magento_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                Magento_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Index_Helper_Data')->__('%s index was rebuilt.', $process->getIndexer()->getName())
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     Mage::helper('Mage_Index_Helper_Data')->__('There was a problem with reindexing process.')
                );
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('Mage_Index_Helper_Data')->__('Cannot initialize the indexer process.')
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
        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer    = Mage::getSingleton('Mage_Index_Model_Indexer');
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_getSession()->addError(Mage::helper('Mage_Index_Helper_Data')->__('Please select Indexes'));
        } else {
            try {
                foreach ($processIds as $processId) {
                    /* @var $process Mage_Index_Model_Process */
                    $process = $indexer->getProcessById($processId);
                    if ($process) {
                        $process->reindexEverything();
                    }
                }
                $count = count($processIds);
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Index_Helper_Data')->__('Total of %d index(es) have reindexed data.', $count)
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('Mage_Index_Helper_Data')->__('Cannot initialize the indexer process.'));
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
            $this->_getSession()->addError(Mage::helper('Mage_Index_Helper_Data')->__('Please select Index(es)'));
        } else {
            try {
                $mode = $this->getRequest()->getParam('index_mode');
                foreach ($processIds as $processId) {
                    /* @var $process Mage_Index_Model_Process */
                    $process = Mage::getModel('Mage_Index_Model_Process')->load($processId);
                    if ($process->getId()) {
                        $process->setMode($mode)
                            ->save();
                    }
                }
                $count = count($processIds);
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Index_Helper_Data')->__('Total of %d index(es) have changed index mode.', $count)
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('Mage_Index_Helper_Data')->__('Cannot initialize the indexer process.'));
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
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('system/index');
    }
}
