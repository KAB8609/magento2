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
 * URL rewrites edit form
 *
 * @method \Magento\Core\Model\Url\Rewrite getUrlRewrite()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Edit\Form setUrlRewrite(\Magento\Core\Model\Url\Rewrite $model)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * @var array
     */
    protected $_sessionData = null;

    /**
     * @var array
     */
    protected $_allStores = null;

    /**
     * @var bool
     */
    protected $_requireStoresFilter = false;

    /**
     * @var array
     */
    protected $_formValues = array();

    /**
     * Set form id and title
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('urlrewrite_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Initialize form values
     * Set form data either from model values or from session
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
     */
    protected function _initFormValues()
    {
        $model = $this->_getModel();
        $this->_formValues = array(
            'store_id'     => $model->getStoreId(),
            'id_path'      => $model->getIdPath(),
            'request_path' => $model->getRequestPath(),
            'target_path'  => $model->getTargetPath(),
            'options'      => $model->getOptions(),
            'description'  => $model->getDescription(),
        );

        $sessionData = $this->_getSessionData();
        if ($sessionData) {
            foreach (array_keys($this->_formValues) as $key) {
                if (isset($sessionData[$key])) {
                    $this->_formValues[$key] = $sessionData[$key];
                }
            }
        }

        return $this;
    }

    /**
     * Prepare the form layout
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
     */
    protected function _prepareForm()
    {
        $this->_initFormValues();

        // Prepare form
        $form = new \Magento\Data\Form(array(
            'id'            => 'edit_form',
            'use_container' => true,
            'method'        => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('URL Rewrite Information')
        ));

        /** @var $typesModel \Magento\Core\Model\Source\Urlrewrite\Types */
        $typesModel = \Mage::getModel('\Magento\Core\Model\Source\Urlrewrite\Types');
        $fieldset->addField('is_system', 'select', array(
            'label'    => __('Type'),
            'title'    => __('Type'),
            'name'     => 'is_system',
            'required' => true,
            'options'  => $typesModel->getAllOptions(),
            'disabled' => true,
            'value'    => $this->_getModel()->getIsSystem()
        ));

        $fieldset->addField('id_path', 'text', array(
            'label'    => __('ID Path'),
            'title'    => __('ID Path'),
            'name'     => 'id_path',
            'required' => true,
            'disabled' => false,
            'value'    => $this->_formValues['id_path']
        ));

        $fieldset->addField('request_path', 'text', array(
            'label'    => __('Request Path'),
            'title'    => __('Request Path'),
            'name'     => 'request_path',
            'required' => true,
            'value'    => $this->_formValues['request_path']
        ));

        $fieldset->addField('target_path', 'text', array(
            'label'    => __('Target Path'),
            'title'    => __('Target Path'),
            'name'     => 'target_path',
            'required' => true,
            'disabled' => false,
            'value'    => $this->_formValues['target_path'],
        ));

        /** @var $optionsModel \Magento\Core\Model\Source\Urlrewrite\Options */
        $optionsModel = \Mage::getModel('\Magento\Core\Model\Source\Urlrewrite\Options');
        $fieldset->addField('options', 'select', array(
            'label'   => __('Redirect'),
            'title'   => __('Redirect'),
            'name'    => 'options',
            'options' => $optionsModel->getAllOptions(),
            'value'   => $this->_formValues['options']
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => __('Description'),
            'title' => __('Description'),
            'name'  => 'description',
            'cols'  => 20,
            'rows'  => 5,
            'value' => $this->_formValues['description'],
            'wrap'  => 'soft'
        ));

        $this->_prepareStoreElement($fieldset);

        $this->setForm($form);
        $this->_formPostInit($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare store element
     *
     * @param \Magento\Data\Form\Element\Fieldset $fieldset
     */
    protected function _prepareStoreElement($fieldset)
    {
        // get store switcher or a hidden field with it's id
        if (\Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'hidden', array(
                'name'  => 'store_id',
                'value' => \Mage::app()->getStore(true)->getId()
            ), 'id_path');
        } else {
            /** @var $renderer \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element */
            $renderer = $this->getLayout()
                ->createBlock('\Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');

            $storeElement = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store'),
                'title'    => __('Store'),
                'name'     => 'store_id',
                'required' => true,
                'values'   => $this->_getRestrictedStoresList(),
                'disabled' => $this->_getModel()->getIsSystem(),
                'value'    => $this->_formValues['store_id'],
            ), 'id_path');
            $storeElement->setRenderer($renderer);
        }
    }

    /**
     * Form post init
     *
     * @param \Magento\Data\Form $form
     * @return \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
     */
    protected function _formPostInit($form)
    {
        $form->setAction(
            \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('*/*/save', array(
                'id' => $this->_getModel()->getId()
            ))
        );

        return $this;
    }

    /**
     * Get session data
     *
     * @return array
     */
    protected function _getSessionData()
    {
        if (is_null($this->_sessionData)) {
            $this->_sessionData = \Mage::getModel('\Magento\Adminhtml\Model\Session')->getData('urlrewrite_data', true);
        }
        return $this->_sessionData;
    }

    /**
     * Get URL rewrite model instance
     *
     * @return \Magento\Core\Model\Url\Rewrite
     */
    protected function _getModel()
    {
        if (!$this->hasData('url_rewrite')) {
            $this->setUrlRewrite(\Mage::getModel('\Magento\Core\Model\Url\Rewrite'));
        }
        return $this->getUrlRewrite();
    }

    /**
     * Get request stores
     *
     * @return array
     */
    protected function _getAllStores()
    {
        if (is_null($this->_allStores)) {
            $this->_allStores = \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm();
        }

        return $this->_allStores;
    }

    /**
     * Get entity stores
     *
     * @return array
     */
    protected function _getEntityStores()
    {
        return $this->_getAllStores();
    }

    /**
     * Get restricted stores list
     * Stores should be filtered only if custom entity is specified.
     * If we use custom rewrite, all stores are accepted.
     *
     * @return array
     */
    protected function _getRestrictedStoresList()
    {
        $stores = $this->_getAllStores();
        $entityStores = $this->_getEntityStores();
        $stores = $this->_getStoresListRestrictedByEntityStores($stores, $entityStores);

        return $stores;
    }

    /**
     * Get stores list restricted by entity stores
     *
     * @param array $stores
     * @param array $entityStores
     * @return array
     */
    private function _getStoresListRestrictedByEntityStores(array $stores, array $entityStores)
    {
        if ($this->_requireStoresFilter) {
            foreach ($stores as $i => $store) {
                if (isset($store['value']) && $store['value']) {
                    $found = false;
                    foreach ($store['value'] as $k => $v) {
                        if (isset($v['value']) && in_array($v['value'], $entityStores)) {
                            $found = true;
                        } else {
                            unset($stores[$i]['value'][$k]);
                        }
                    }
                    if (!$found) {
                        unset($stores[$i]);
                    }
                }
            }
        }

        return $stores;
    }
}
