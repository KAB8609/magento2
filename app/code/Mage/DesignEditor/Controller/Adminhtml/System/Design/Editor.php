<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend controller for the design editor
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Controller_Adminhtml_System_Design_Editor extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Theme_Model_Config
     */
    protected $_themeConfig;

    /**
     * @var Mage_Theme_Model_Config_Customization
     */
    protected $_customizationConfig;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Mage_Theme_Model_Config $themeConfig
     * @param Mage_Theme_Model_Config_Customization $customizationConfig
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Mage_Theme_Model_Config $themeConfig,
        Mage_Theme_Model_Config_Customization $customizationConfig
    ) {
        $this->_themeConfig         = $themeConfig;
        $this->_customizationConfig = $customizationConfig;

        parent::__construct($context);
    }

    /**
     * Display the design editor launcher page
     */
    public function indexAction()
    {
        if (!$this->_resolveForwarding()) {
            $this->_renderStoreDesigner();
        }
    }

    /**
     * Ajax loading available themes
     */
    public function loadThemeListAction()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        $page = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()
            ->getParam('page_size', Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE);

        try {
            $this->loadLayout();
            /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
            $collection = $this->_objectManager->get('Mage_Core_Model_Resource_Theme_Collection')
                ->filterPhysicalThemes($page, $pageSize);

            /** @var $availableThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available */
            $availableThemeBlock =  $this->getLayout()->getBlock('available.theme.list');
            $availableThemeBlock->setCollection($collection)->setNextPage(++$page);
            $availableThemeBlock->setIsFirstEntrance($this->_isFirstEntrance());
            $availableThemeBlock->setHasThemeAssigned($this->_customizationConfig->hasThemeAssigned());

            $response = array('content' => $this->getLayout()->getOutput());
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => $this->_helper->__('Sorry, but we can\'t load the theme list.'));
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $mode = (string)$this->getRequest()->getParam('mode', Mage_DesignEditor_Model_State::MODE_NAVIGATION);
        try {
            /** @var Mage_DesignEditor_Model_Theme_Context $themeContext */
            $themeContext = $this->_objectManager->get('Mage_DesignEditor_Model_Theme_Context');
            $themeContext->setEditableThemeById($themeId);
            $launchedTheme = $themeContext->getEditableTheme();
            if ($launchedTheme->isPhysical()) {
                $launchedTheme = $launchedTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_PHYSICAL)
                    ->createVirtualTheme($launchedTheme);
                $this->_redirect($this->getUrl('*/*/*', array('theme_id' => $launchedTheme->getId())));
                return;
            }
            $editableTheme = $themeContext->getStagingTheme();

            $this->_eventManager->dispatch('design_editor_activate');

            $this->_setTitle();
            $this->loadLayout();

            $this->_configureToolbarBlocks($launchedTheme, $editableTheme, $mode); //top panel
            $this->_configureToolsBlocks($launchedTheme, $mode); //bottom panel
            $this->_configureEditorBlock($launchedTheme, $mode); //editor container

            /** @var $storeViewBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView */
            $storeViewBlock = $this->getLayout()->getBlock('theme.selector.storeview');
            $storeViewBlock->setData(array(
                'actionOnAssign' => 'none',
                'theme_id'       => $launchedTheme->getId()
            ));

            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Sorry, there was an unknown error.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * Assign theme to list of store views
     */
    public function assignThemeToStoreAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $reportToSession = (bool)$this->getRequest()->getParam('reportToSession');

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        $hadThemeAssigned = $this->_customizationConfig->hasThemeAssigned();

        try {
            $theme = $this->_loadThemeById($themeId);

            $themeCustomization = $theme->isVirtual()
                ? $theme
                : $theme->getDomainModel(Mage_Core_Model_Theme::TYPE_PHYSICAL)->createVirtualTheme($theme);

            /** @var $themeCustomization Mage_Core_Model_Theme */
            $this->_themeConfig->assignToStore($themeCustomization, $this->_getStores());

            $successMessage = $hadThemeAssigned
                ? $this->__('You assigned a new theme to your store view.')
                : $this->__('You assigned a theme to your live store.');
            if ($reportToSession) {
                $this->_getSession()->addSuccess($successMessage);
            }
            $response = array(
                'message' => $successMessage,
                'themeId' => $themeCustomization->getId()
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('error' => $this->_helper->__('This theme is not assigned.'))
            ));
            $response = array(
                'error'   => true,
                'message' => $this->_helper->__('This theme is not assigned.')
            );
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Rename title action
     */
    public function quickEditAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $themeTitle = (string)$this->getRequest()->getParam('theme_title');

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        try {
            $theme = $this->_loadThemeById($themeId);
            if (!$theme->isEditable()) {
                throw new Mage_Core_Exception($this->__('Sorry, but you can\'t edit theme "%s".',
                    $theme->getThemeTitle()));
            }
            $theme->setThemeTitle($themeTitle);
            $theme->save();
            $response = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->__('This theme is not saved.'));
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Display available theme list. Only when no customized themes
     */
    public function firstEntranceAction()
    {
        if (!$this->_resolveForwarding()) {
            $this->_renderStoreDesigner();
        }
    }

    /**
     * Apply changes from 'staging' theme to 'virtual' theme
     */
    public function saveAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');

        /** @var Mage_DesignEditor_Model_Theme_Context $themeContext */
        $themeContext = $this->_objectManager->get('Mage_DesignEditor_Model_Theme_Context');
        $themeContext->setEditableThemeById($themeId);
        try {
            $themeContext->copyChanges();
            if ($this->_customizationConfig->isThemeAssignedToStore($themeContext->getEditableTheme())) {
                $message = $this->__('You updated your live store.');
            } else {
                $message = $this->__('You saved updates to this theme.');
            }
            $response = array('message' =>  $message);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->_helper->__('Sorry, there was an unknown error.'));
        }

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Duplicate theme action
     */
    public function duplicateAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        /** @var $themeCopy Mage_Core_Model_Theme */
        $themeCopy = $this->_objectManager->create('Mage_Core_Model_Theme');
        /** @var $copyService Mage_Core_Model_Theme_CopyService */
        $copyService = $this->_objectManager->get('Mage_Core_Model_Theme_CopyService');
        try {
            $theme = $this->_loadThemeById($themeId);
            if (!$theme->isVirtual()) {
                throw new Mage_Core_Exception($this->__('Sorry, but you can\'t edit theme "%s".',
                    $theme->getThemeTitle()));
            }
            $themeCopy->setData($theme->getData());
            $themeCopy->setId(null)->setThemeTitle($coreHelper->__('Copy of [%s]', $theme->getThemeTitle()));
            $themeCopy->getThemeImage()->createPreviewImageCopy($theme->getPreviewImage());
            $themeCopy->save();
            $copyService->copy($theme, $themeCopy);
            $this->_getSession()->addSuccess(
                $this->__('You saved a duplicate copy of this theme in "My Customizations."')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_getSession()->addError($this->__('You cannot duplicate this theme.'));
        }
        $this->_redirectUrl($this->_getRefererUrl());
    }

    /**
     * Revert 'staging' theme to the state of 'physical' or 'virtual'
     *
     * @throws Mage_Core_Exception
     */
    public function revertAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $revertTo = $this->getRequest()->getParam('revert_to');

        $virtualTheme = $this->_loadThemeById($themeId);
        if (!$virtualTheme->isVirtual()) {
            throw new Mage_Core_Exception($this->_helper->__('Theme "%s" is not editable.', $virtualTheme->getId()));
        }

        try {
            /** @var $copyService Mage_Core_Model_Theme_CopyService */
            $copyService = $this->_objectManager->get('Mage_Core_Model_Theme_CopyService');
            $stagingTheme = $virtualTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)->getStagingTheme();
            switch ($revertTo) {
                case 'last_saved':
                    $copyService->copy($virtualTheme, $stagingTheme);
                    $message = $this->_helper->__('Theme "%s" reverted to last saved state',
                        $virtualTheme->getThemeTitle()
                    );
                    break;

                case 'physical':
                    $physicalTheme = $virtualTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)
                        ->getPhysicalTheme();
                    $copyService->copy($physicalTheme, $stagingTheme);
                    $message = $this->_helper->__('Theme "%s" reverted to last default state',
                        $virtualTheme->getThemeTitle()
                    );
                    break;

                default:
                    throw new Magento_Exception('Invalid revert mode "%s"', $revertTo);
            }
            $response = array('message' => $message);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->_helper->__('Unknown error'));
        }
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Set page title
     */
    protected function _setTitle()
    {
        $this->_title($this->__('Store Designer'));
    }

    /**
     * Load theme by id
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _loadThemeById($themeId)
    {
        /** @var $themeFactory Mage_Core_Model_Theme_FlyweightFactory */
        $themeFactory = $this->_objectManager->create('Mage_Core_Model_Theme_FlyweightFactory');
        $theme = $themeFactory->create($themeId);
        if (empty($theme)) {
            throw new Mage_Core_Exception($this->__('We can\'t find this theme.'));
        }
        return $theme;
    }

    /**
     * Whether the current user has enough permissions to execute an action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_DesignEditor::editor');
    }

    /**
     * Pass data to the Tools panel blocks that is needed it for rendering
     *
     * @param Mage_Core_Model_Theme $theme
     * @param string $mode
     * @return Mage_DesignEditor_Controller_Adminhtml_System_Design_Editor
     */
    protected function _configureToolsBlocks($theme, $mode)
    {
        /** @var $toolsBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools */
        $toolsBlock = $this->getLayout()->getBlock('design_editor_tools');
        if ($toolsBlock) {
            $toolsBlock->setMode($mode);
        }

        /** @var $cssTabBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Css */
        $cssTabBlock = $this->getLayout()->getBlock('design_editor_tools_code_css');
        if ($cssTabBlock) {
            /** @var $helper Mage_Core_Helper_Theme */
            $helper = $this->_objectManager->get('Mage_Core_Helper_Theme');
            $cssFiles = $helper->getGroupedCssFiles($theme);
            $cssTabBlock->setCssFiles($cssFiles)
                ->setThemeId($theme->getId());
        }
        return $this;
    }

    /**
     * Pass data to the Toolbar panel blocks that is needed for rendering
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme $editableTheme
     * @param string $mode
     * @return Mage_DesignEditor_Controller_Adminhtml_System_Design_Editor
     */
    protected function _configureToolbarBlocks($theme, $editableTheme, $mode)
    {
        /** @var $toolbarBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons */
        $toolbarBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons');
        $toolbarBlock->setThemeId($editableTheme->getId())->setVirtualThemeId($theme->getId())
            ->setMode($mode);

        /** @var $saveButtonBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save */
        $saveButtonBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons_save');
        if ($saveButtonBlock) {
            $saveButtonBlock->setTheme($theme)->setMode($mode)->setHasThemeAssigned(
                $this->_customizationConfig->hasThemeAssigned()
            );
        }
        /** @var $saveButtonBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Edit */
        $editButtonBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons_edit');
        if ($editButtonBlock) {
            $editButtonBlock->setTheme($editableTheme);
        }

        return $this;
    }

    /**
     * Set to iframe block selected mode and theme
     *
     * @param Mage_Core_Model_Theme $editableTheme
     * @param string $mode
     * @return Mage_DesignEditor_Controller_Adminhtml_System_Design_Editor
     */
    protected function _configureEditorBlock($editableTheme, $mode)
    {
        /** @var $editorBlock Mage_DesignEditor_Block_Adminhtml_Editor_Container */
        $editorBlock = $this->getLayout()->getBlock('design_editor');
        $currentUrl = $this->_getCurrentUrl($editableTheme->getId(), $mode);
        $editorBlock->setFrameUrl($currentUrl);
        $editorBlock->setTheme($editableTheme);

        return $this;
    }

    /**
     * Check whether is customized themes in database
     *
     * @return bool
     */
    protected function _isFirstEntrance()
    {
        $isCustomized = (bool)$this->_objectManager->get('Mage_Core_Model_Resource_Theme_CollectionFactory')->create()
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL)
            ->getSize();
        return !$isCustomized;
    }

    /**
     * Load layout
     */
    protected function _renderStoreDesigner()
    {
        try {
            $this->_setTitle();
            $this->loadLayout();
            $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');
            if (!$this->_isFirstEntrance()) {
                /** @var $assignedThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Assigned */
                $assignedThemeBlock = $this->getLayout()->getBlock('assigned.theme.list');
                $assignedThemeBlock->setCollection($this->_customizationConfig->getAssignedThemeCustomizations());

                /** @var $unassignedThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Unassigned */
                $unassignedThemeBlock = $this->getLayout()->getBlock('unassigned.theme.list');
                $unassignedThemeBlock->setCollection($this->_customizationConfig->getUnassignedThemeCustomizations());
                $unassignedThemeBlock->setHasThemeAssigned($this->_customizationConfig->hasThemeAssigned());
            }
            /** @var $storeViewBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView */
            $storeViewBlock = $this->getLayout()->getBlock('theme.selector.storeview');
            $storeViewBlock->setData('actionOnAssign', 'refresh');
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('We can\'t load the list of themes.'));
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Resolve which action should be actually performed and forward to it
     *
     * @return bool Is forwarding was done
     */
    protected function _resolveForwarding()
    {
        $action = $this->_isFirstEntrance() ? 'firstEntrance' : 'index';
        if ($action != $this->getRequest()->getActionName()) {
            $this->_forward($action);
            return true;
        };

        return false;
    }

    /**
     * Get current url
     *
     * @param null|string $themeId
     * @param null|string $mode
     * @return string
     */
    protected function _getCurrentUrl($themeId = null, $mode = null)
    {
        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_NavigationMode */
        $vdeUrlModel = $this->_objectManager->create('Mage_DesignEditor_Model_Url_NavigationMode', array(
             'data' => array('mode' => $mode, 'themeId' => $themeId)
        ));
        $url = $this->_getSession()->getData(Mage_DesignEditor_Model_State::CURRENT_URL_SESSION_KEY);
        if (empty($url)) {
            $url = '';
        }
        return $vdeUrlModel->getUrl(ltrim($url, '/'));
    }

    /**
     * Get stores
     *
     * @todo temporary method. used until we find a way to convert array to JSON on JS side
     *
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getStores()
    {
        $stores = $this->getRequest()->getParam('stores');

        $defaultStore = -1;
        $emptyStores = -2;
        if ($stores == $defaultStore) {
            $ids = array_keys(Mage::app()->getStores());
            $stores = array(array_shift($ids));
        } elseif ($stores == $emptyStores) {
            $stores = array();
        }

        if (!is_array($stores)) {
            throw new InvalidArgumentException('Param "stores" is not valid');
        }

        return $stores;
    }
}