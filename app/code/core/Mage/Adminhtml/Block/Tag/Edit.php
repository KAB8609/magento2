<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin tag edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'tag_id';
        $this->_controller = 'tag';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('tag')->__('Save Tag'));
        $this->_updateButton('delete', 'label', Mage::helper('tag')->__('Delete Tag'));

        $this->addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('tag')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                'class'     => 'save'
            )
        );
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/tag_store_switcher'));

        $this->setChild('tag_assign_accordion', $this->getLayout()->createBlock('adminhtml/tag_edit_assigned'));

        $this->setChild('accordion', $this->getLayout()->createBlock('adminhtml/tag_edit_accordion'));

        return $this;
    }

    public function getHeaderText()
    {
        if (Mage::registry('tag_tag')->getId()) {
            return Mage::helper('tag')->__("Edit Tag '%s'", $this->htmlEscape(Mage::registry('tag_tag')->getName()));
        }
        return Mage::helper('tag')->__('New Tag');
    }

    /**
     * Retrieves Accordions HTML
     *
     * @return string
     */
    public function getAcordionsHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * Retrieves Assigned Tags Accordion HTML
     *
     * @return string
     */
    public function getTagAssignAccordionHtml()
    {
        return $this->getChildHtml('tag_assign_accordion');
    }

    /**
     * Retrieves Store Switcher HTML
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Retrieves Tag Save URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    /**
     * Retrieves Tag SaveAndContinue URL
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'ret' => 'edit'));
    }
}
