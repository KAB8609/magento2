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
 * Admin tax rate save toolbar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Tax\Rate\Toolbar;

class Save extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'tax/toolbar/rate/save.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->assign('createUrl', $this->getUrl('adminhtml/tax_rate/save'));

    }

    protected function _prepareLayout()
    {
        $this->addChild('backButton', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\''.$this->getUrl('adminhtml/*/').'\'',
            'class' => 'back'
        ));

        $this->addChild('resetButton', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('saveButton', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Save Rate'),
            'class' => 'save'
        ));

        $this->addChild('deleteButton', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete Rate'),
            'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . $this->getUrl('adminhtml/*/delete', array('rate' => $this->getRequest()->getParam('rate'))) . '\')',
            'class' => 'delete'
        ));
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        $formId = $this->getLayout()->getBlock('tax_rate_form')->getDestElementId();
        $button = $this->getChildBlock('saveButton');
        $button->setDataAttribute(array(
            'mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#' . $formId),
            ),
        ));
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if( intval($this->getRequest()->getParam('rate')) == 0 ) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }
}
