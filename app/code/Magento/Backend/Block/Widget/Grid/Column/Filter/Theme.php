<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Theme
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Abstract
{
    /**
     * Retrieve filter HTML
     *
     * @return string
     */
    public function getHtml()
    {
        $options = $this->getOptions();
        if ($this->getColumn()->getWithEmpty()) {
            array_unshift($options, array(
                'value' => '',
                'label' => ''
            ));
        }
        $html = sprintf(
            '<select name="%s" id="%s" class="no-changes" %s>%s</select>',
            $this->_getHtmlName(),
            $this->_getHtmlId(),
            $this->getUiId('filter', $this->_getHtmlName()),
            $this->_drawOptions($options)
        );
        return $html;
    }

    /**
     * Retrieve options setted in column.
     * Or load if options was not set.
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getColumn()->getOptions();
        if (empty($options) || !is_array($options)) {
            /** @var $label Magento_Core_Model_Theme_Label */
            $label = Mage::getModel('Magento_Core_Model_Theme_Label');
            $options = $label->getLabelsCollection();
        }
        return $options;
    }

    /**
     * Render SELECT options
     *
     * @param array $options
     * @return string
     */
    protected function _drawOptions($options)
    {
        if (empty($options) || !is_array($options)) {
            return '';
        }

        $value = $this->getValue();
        $html  = '';

        foreach ($options as $option) {
            if (!isset($option['value']) || !isset($option['label'])) {
                continue;
            }
            if (is_array($option['value'])) {
                $html .= '<optgroup label="'.$option['label'].'">'
                    . $this->_drawOptions($option['value'])
                    . '</optgroup>';
            } else {
                $selected = (($option['value'] == $value && (!is_null($value))) ? ' selected="selected"' : '');
                $html .= '<option value="'.$option['value'].'"'.$selected.'>'.$option['label'].'</option>';
            }
        }

        return $html;
    }

    /**
     * Retrieve filter condition for collection
     *
     * @return mixed
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }
        $value = $this->getValue();
        if ($value == 'all') {
            $value = '';
        }
        return array('eq' => $value);
    }
}
