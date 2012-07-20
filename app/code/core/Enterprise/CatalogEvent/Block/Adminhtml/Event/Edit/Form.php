<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Return form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Prepares layout, set custom renderers
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Enterprise_CatalogEvent_Block_Adminhtml_Form_Renderer_Fieldset_Element')
        );
    }

    /**
     * Prepares event edit form
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getActionUrl(),
                'method'  => 'post',
                'field_name_suffix' => 'catalogevent',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setHtmlIdPrefix('event_edit_');

        $fieldset = $form->addFieldset('general_fieldset',
            array(
                'legend' => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Catalog Event Information'),
                'class'  => 'fieldset-wide'
            )
        );

        $this->_addElementTypes($fieldset);

        $currentCategory = Mage::getModel('Mage_Catalog_Model_Category')
            ->load($this->getEvent()->getCategoryId());

        $fieldset->addField('category_name', 'note',
            array(
                'id'    => 'category_span',
                'label' => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Category')
            )
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $fieldset->addField('date_start', 'date', array(
                'label'        => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Start Date'),
                'name'         => 'date_start',
                'required'     => true, 'time' => true,
                'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => $dateFormatIso
            ));

        $fieldset->addField('date_end', 'date', array(
                'label'        => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('End Date'),
                'name'         => 'date_end', 'required' => true,
                'time'         => true,
                'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => $dateFormatIso
            ));

        $fieldset->addField('image', 'image', array(
                'label' => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Image'),
                'scope' => 'store',
                'name'  => 'image'
             )
        );

        $fieldset->addField('sort_order', 'text', array(
                'label' => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Sort Order'),
                'name'  => 'sort_order',
                'class' => 'validate-num qty'
             )
        );

        $statuses = array(
            Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Upcoming'),
            Enterprise_CatalogEvent_Model_Event::STATUS_OPEN => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Open'),
            Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Closed')
        );

        $fieldset->addField('display_state_array', 'checkboxes', array(
                'label'  => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Display Countdown Ticker On'),
                'name'   => 'display_state[]',
                'values' => array(
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Category Page'),
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('Product Page')
                )
            ));

        if ($this->getEvent()->getId()) {
            $fieldset->addField('status', 'note', array(
                    'label' => Mage::helper('Enterprise_CatalogEvent_Helper_Data')->__('BugsCoverage'),
                    'text'  => ($this->getEvent()->getStatus() ? $statuses[$this->getEvent()->getStatus()] : $statuses[Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING])
            ));
        }

        $form->setValues($this->getEvent()->getData());

        if ($currentCategory && $this->getEvent()->getId()) {
            $form->getElement('category_name')->setText(
                '<a href="' . Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('adminhtml/catalog_category/edit',
                                                            array('clear' => 1, 'id' => $currentCategory->getId()))
                . '">' . $currentCategory->getName() . '</a>'
            );
        } else {
            $form->getElement('category_name')->setText(
                '<a href="' . $this->getParentBlock()->getBackUrl()
                . '">' . $currentCategory->getName() . '</a>'
            );
        }

        $form->getElement('date_start')->setValue($this->getEvent()->getStoreDateStart());
        $form->getElement('date_end')->setValue($this->getEvent()->getStoreDateEnd());

        if ($this->getEvent()->getDisplayState()) {
            $form->getElement('display_state_array')->setChecked($this->getEvent()->getDisplayState());
        }

        $form->setUseContainer(true);
        $form->setDataObject($this->getEvent());
        $this->setForm($form);

        if ($this->getEvent()->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                if ($element->getId() !== 'image') {
                    $element->setReadonly(true, true);
                }
            }
        }

        if ($this->getEvent()->getImageReadonly()) {
            $form->getElement('image')->setReadonly(true, true);
        }
        return parent::_prepareForm();
    }

    /**
     * Retrieve catalog event model
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        return Mage::registry('enterprise_catalogevent_event');
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()
                    ->getBlockClassName('Enterprise_CatalogEvent_Block_Adminhtml_Event_Helper_Image')
        );
    }

}
