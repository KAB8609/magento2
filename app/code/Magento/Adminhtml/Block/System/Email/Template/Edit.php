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
 * Adminhtml system template edit block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method array getTemplateOptions()
 */
class Magento_Adminhtml_Block_System_Email_Template_Edit extends Magento_Adminhtml_Block_Widget
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @var Magento_Backend_Model_Menu_Config
     */
    protected $_menuConfig;

    /**
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'system/email/template/edit.phtml';

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Backend_Model_Menu_Config $menuConfig
     * @param Magento_Backend_Model_Config_Structure $configStructure
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Registry $registry,
        Magento_Backend_Model_Menu_Config $menuConfig,
        Magento_Backend_Model_Config_Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->_registryManager = $registry;
        $this->_menuConfig = $menuConfig;
        $this->_configStructure = $configStructure;
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class'   => 'back'
                    )
                )
        );


        $this->setChild('reset_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Reset'),
                        'onclick' => 'window.location.href = window.location.href'
                    )
                )
        );


        $this->setChild('delete_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Delete Template'),
                        'onclick' => 'templateControl.deleteTemplate();',
                        'class'   => 'delete'
                    )
                )
        );

        $this->setChild('to_plain_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Convert to Plain Text'),
                        'onclick' => 'templateControl.stripTags();',
                        'id'      => 'convert_button'
                    )
                )
        );


        $this->setChild('to_html_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Return Html Version'),
                        'onclick' => 'templateControl.unStripTags();',
                        'id'      => 'convert_button_back',
                        'style'   => 'display:none'
                    )
                )
        );

        $this->setChild('toggle_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Toggle Editor'),
                        'onclick' => 'templateControl.toggleEditor();',
                        'id'      => 'toggle_button'
                    )
                )
        );


        $this->setChild('preview_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Preview Template'),
                        'onclick' => 'templateControl.preview();'
                    )
                )
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Save Template'),
                        'onclick' => 'templateControl.save();',
                        'class'   => 'save'
                    )
                )
        );

        $this->setChild('load_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Load Template'),
                        'onclick' => 'templateControl.load();',
                        'type'    => 'button',
                        'class'   => 'save'
                    )
                )
        );


        $this->addChild('form', 'Magento_Adminhtml_Block_System_Email_Template_Edit_Form');
        return parent::_prepareLayout();
    }

    /**
     * Collect, sort and set template options
     *
     * @return Magento_Adminhtml_Block_System_Email_Template_Edit
     */
    protected function _beforeToHtml()
    {
        $groupedOptions = array();
        foreach (Magento_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray() as $option) {
            $groupedOptions[$option['group']][] = $option;
        }
        ksort($groupedOptions);
        $this->setData('template_options', $groupedOptions);

        return parent::_beforeToHtml();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getToggleButtonHtml()
    {
        return $this->getChildHtml('toggle_button');
    }


    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getToPlainButtonHtml()
    {
        return $this->getChildHtml('to_plain_button');
    }

    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml('to_html_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getLoadButtonHtml()
    {
        return $this->getChildHtml('load_button');
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        return $this->getEmailTemplate()->getId();
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if($this->getEditMode()) {
          return __('Edit Email Template');
        }

        return  __('New Email Template');
    }


    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    public function isTextType()
    {
        return $this->getEmailTemplate()->isPlain();
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }

    /**
     * Retrive email template model
     *
     * @return Magento_Core_Model_Email_Template
     */
    public function getEmailTemplate()
    {
        return $this->_registryManager->registry('current_email_template');
    }

    /**
     * Load template url
     *
     * @return string
     */
    public function getLoadUrl()
    {
        return $this->getUrl('*/*/defaultTemplate');
    }

    /**
     * Get paths of where current template is used as default
     *
     * @param bool $asJSON
     * @return string
     */
    public function getUsedDefaultForPaths($asJSON = true)
    {
        /** @var $template Magento_Adminhtml_Model_Email_Template */
        $template = $this->getEmailTemplate();
        $paths = $template->getSystemConfigPathsWhereUsedAsDefault();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if($asJSON){
            return $this->helper('Magento_Core_Helper_Data')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Get paths of where current template is currently used
     *
     * @param bool $asJSON
     * @return string
     */
    public function getUsedCurrentlyForPaths($asJSON = true)
    {
        /** @var $template Magento_Adminhtml_Model_Email_Template */
        $template = $this->getEmailTemplate();
        $paths = $template->getSystemConfigPathsWhereUsedCurrently();
        $pathsParts = $this->_getSystemConfigPathsParts($paths);
        if($asJSON){
            return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($pathsParts);
        }
        return $pathsParts;
    }

    /**
     * Convert xml config pathes to decorated names
     *
     * @param array $paths
     * @return array
     */
    protected function _getSystemConfigPathsParts($paths)
    {
        $result = $urlParams = $prefixParts = array();
        $scopeLabel = __('GLOBAL');
        if ($paths) {
            /** @var $menu Magento_Backend_Model_Menu */
            $menu = $this->_menuConfig->getMenu();
            $item = $menu->get('Magento_Adminhtml::system');
            // create prefix path parts
            $prefixParts[] = array(
                'title' => __($item->getTitle()),
            );
            $item = $menu->get('Magento_Adminhtml::system_config');
            $prefixParts[] = array(
                'title' => __($item->getTitle()),
                'url' => $this->getUrl('adminhtml/system_config/'),
            );

            $pathParts = $prefixParts;
            foreach ($paths as $pathData) {
                $pathDataParts = explode('/', $pathData['path']);
                $sectionName = array_shift($pathDataParts);

                $urlParams = array('section' => $sectionName);
                if (isset($pathData['scope']) && isset($pathData['scope_id'])) {
                    switch ($pathData['scope']) {
                        case 'stores':
                            $store = Mage::app()->getStore($pathData['scope_id']);
                            if ($store) {
                                $urlParams['website'] = $store->getWebsite()->getCode();
                                $urlParams['store'] = $store->getCode();
                                $scopeLabel = $store->getWebsite()->getName() . '/' . $store->getName();
                            }
                            break;
                        case 'websites':
                            $website = Mage::app()->getWebsite($pathData['scope_id']);
                            if ($website) {
                                $urlParams['website'] = $website->getCode();
                                $scopeLabel = $website->getName();
                            }
                            break;
                        default:
                            break;
                    }
                }
                $pathParts[] = array(
                    'title' => $this->_configStructure->getElement($sectionName)->getLabel(),
                    'url' => $this->getUrl('adminhtml/system_config/edit', $urlParams),
                );
                $elementPathParts = array($sectionName);
                while (count($pathDataParts) != 1) {
                    $elementPathParts[] = array_shift($pathDataParts);
                    $pathParts[] = array(
                        'title' => $this->_configStructure
                            ->getElementByPathParts($elementPathParts)
                            ->getLabel()
                    );
                }
                $elementPathParts[] = array_shift($pathDataParts);
                $pathParts[] = array(
                    'title' => $this->_configStructure
                        ->getElementByPathParts($elementPathParts)
                        ->getLabel(),
                    'scope' => $scopeLabel
                );
                $result[] = $pathParts;
                $pathParts = $prefixParts;
            }
        }
        return $result;
    }

    /**
     * Return original template code of current template
     *
     * @return string
     */
    public function getOrigTemplateCode()
    {
        return $this->getEmailTemplate()->getOrigTemplateCode();
    }
}
