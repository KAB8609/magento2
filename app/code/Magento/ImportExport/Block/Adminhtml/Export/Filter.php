<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export filter block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Block_Adminhtml_Export_Filter extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Helper object.
     *
     * @var Magento_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Set grid parameters.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_helper = Mage::helper('Magento_ImportExport_Helper_Data');

        $this->setRowClickCallback(null);
        $this->setId('export_filter_grid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setPagerVisibility(false);
        $this->setDefaultLimit(null);
        $this->setUseAjax(true);
    }

    /**
     * Date 'from-to' filter HTML with values
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getDateFromToHtmlWithValue(Magento_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $arguments = array(
            'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
            'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
            'class'        => 'input-text input-text-range-date',
            'date_format'  => Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT),
            'image'        => $this->getViewFileUrl('images/grid-cal.gif')
        );
        /** @var $selectBlock Magento_Core_Block_Html_Date */
        $dateBlock = $this->_layout->getBlockFactory()->createBlock(
            'Magento_Core_Block_Html_Date', array('data' => $arguments)
        );
        $fromValue = null;
        $toValue   = null;
        if (is_array($value) && count($value) == 2) {
            $fromValue = $this->_helper->escapeHtml(reset($value));
            $toValue   = $this->_helper->escapeHtml(next($value));
        }


        return '<strong>' . __('From') . ':</strong>&nbsp;'
            . $dateBlock->setValue($fromValue)->getHtml()
            . '&nbsp;<strong>' . __('To') . ':</strong>&nbsp;'
            . $dateBlock->setId($dateBlock->getId() . '_to')->setValue($toValue)->getHtml();
    }

    /**
     * Input text filter HTML with value
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getInputHtmlWithValue(Magento_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $html = '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '" class="input-text input-text-export-filter"';
        if ($value) {
            $html .= ' value="' . $this->_helper->escapeHtml($value) . '"';
        }

        return $html . ' />';
    }

    /**
     * Multiselect field filter HTML with selected values
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getMultiSelectHtmlWithValue(Magento_Eav_Model_Entity_Attribute $attribute, $value)
    {
        if ($attribute->getFilterOptions()) {
            $options = $attribute->getFilterOptions();
        } else {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $optionParams) {
                if ('' === $optionParams['value']) {
                    unset($options[$key]);
                    break;
                }
            }
        }
        if (($size = count($options))) {
            $arguments = array(
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()). '[]',
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'multiselect multiselect-export-filter',
                'extra_params' => 'multiple="multiple" size="' . ($size > 5 ? 5 : ($size < 2 ? 2 : $size))
            );
            /** @var $selectBlock Magento_Core_Block_Html_Select */
            $selectBlock = $this->_layout->getBlockFactory()->createBlock(
                'Magento_Core_Block_Html_Select', array('data' => $arguments)
            );
            return $selectBlock->setOptions($options)
                ->setValue($value)
                ->getHtml();
        } else {
            return __('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Number 'from-to' field filter HTML with selected value.
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getNumberFromToHtmlWithValue(Magento_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $fromValue = null;
        $toValue = null;
        $name = $this->getFilterElementName($attribute->getAttributeCode());
        if (is_array($value) && count($value) == 2) {
            $fromValue = $this->_helper->escapeHtml(reset($value));
            $toValue   = $this->_helper->escapeHtml(next($value));
        }

        return '<strong>' . __('From') . ':</strong>&nbsp;'
             . '<input type="text" name="' . $name . '[]" class="input-text input-text-range"'
             . ' value="' . $fromValue . '"/>&nbsp;'
             . '<strong>' . __('To')
             . ':</strong>&nbsp;<input type="text" name="' . $name
             . '[]" class="input-text input-text-range" value="' . $toValue . '" />';
    }

    /**
     * Select field filter HTML with selected value.
     *
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getSelectHtmlWithValue(Magento_Eav_Model_Entity_Attribute $attribute, $value)
    {
        if ($attribute->getFilterOptions()) {
            $options = array();

            foreach ($attribute->getFilterOptions() as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
        } else {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        if (($size = count($options))) {
            // add empty vaue option
            $firstOption = reset($options);

            if ('' === $firstOption['value']) {
                $options[key($options)]['label'] = '';
            } else {
                array_unshift($options, array('value' => '', 'label' => ''));
            }
            $arguments = array(
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()),
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'select select-export-filter'
            );
            /** @var $selectBlock Magento_Core_Block_Html_Select */
            $selectBlock = $this->_layout->getBlockFactory()->createBlock(
                'Magento_Core_Block_Html_Select', array('data' => $arguments)
            );
            return $selectBlock->setOptions($options)
                ->setValue($value)
                ->getHtml();
        } else {
            return __('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Add columns to grid
     *
     * @return Magento_ImportExport_Block_Adminhtml_Export_Filter
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('skip', array(
            'header'     => __('Exclude'),
            'type'       => 'checkbox',
            'name'       => 'skip',
            'field_name' => Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP . '[]',
            'filter'     => false,
            'sortable'   => false,
            'align'      => 'center',
            'index'      => 'attribute_id'
        ));
        $this->addColumn('frontend_label', array(
            'header'   => __('Attribute Label'),
            'index'    => 'frontend_label',
            'sortable' => false,
        ));
        $this->addColumn('attribute_code', array(
            'header' => __('Attribute Code'),
            'index'  => 'attribute_code'
        ));
        $this->addColumn('filter', array(
            'header'         => __('Filter'),
            'sortable'       => false,
            'filter'         => false,
            'frame_callback' => array($this, 'decorateFilter')
        ));

        if ($this->hasOperation()) {
            $operation = $this->getOperation();
            $skipAttr = $operation->getSkipAttr();
            if ($skipAttr) {
                $this->getColumn('skip')
                    ->setData('values', $skipAttr);
            }
            $filter = $operation->getExportFilter();
            if ($filter) {
                $this->getColumn('filter')
                    ->setData('values', $filter);
            }
        }

        return $this;
    }

    /**
     * Create filter fields for 'Filter' column.
     *
     * @param mixed $value
     * @param Magento_Eav_Model_Entity_Attribute $row
     * @param Magento_Object $column
     * @param boolean $isExport
     * @return string
     */
    public function decorateFilter($value, Magento_Eav_Model_Entity_Attribute $row, Magento_Object $column, $isExport)
    {
        $value  = null;
        $values = $column->getValues();
        if (is_array($values) && isset($values[$row->getAttributeCode()])) {
            $value = $values[$row->getAttributeCode()];
        }
        switch (Magento_ImportExport_Model_Export::getAttributeFilterType($row)) {
            case Magento_ImportExport_Model_Export::FILTER_TYPE_SELECT:
                $cell = $this->_getSelectHtmlWithValue($row, $value);
                break;
            case Magento_ImportExport_Model_Export::FILTER_TYPE_INPUT:
                $cell = $this->_getInputHtmlWithValue($row, $value);
                break;
            case Magento_ImportExport_Model_Export::FILTER_TYPE_DATE:
                $cell = $this->_getDateFromToHtmlWithValue($row, $value);
                break;
            case Magento_ImportExport_Model_Export::FILTER_TYPE_NUMBER:
                $cell = $this->_getNumberFromToHtmlWithValue($row, $value);
                break;
            default:
                $cell = __('Unknown attribute filter type');
        }
        return $cell;
    }

    /**
     * Element filter ID getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementId($attributeCode)
    {
        return Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "_{$attributeCode}";
    }

    /**
     * Element filter full name getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementName($attributeCode)
    {
        return Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "[{$attributeCode}]";
    }

    /**
     * Get row edit URL.
     *
     * @return string|boolean
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Prepare collection by setting page number, sorting etc..
     *
     * @param Magento_Data_Collection $collection
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function prepareCollection(Magento_Data_Collection $collection)
    {
        $this->setCollection($collection);
        return $this->getCollection();
    }
}