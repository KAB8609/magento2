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
 * Backend system config array field renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

class Regexceptions
    extends \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Initialise form fields
     */
    protected function _construct()
    {
        $this->addColumn('search', array(
            'label' => __('Search String'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => __('Design Theme'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add \Exception');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'value' && isset($this->_columns[$columnName])) {
            /** @var $label \Magento\Core\Model\Theme\Label */
            $label = \Mage::getModel('\Magento\Core\Model\Theme\Label');
            $options = $label->getLabelsCollection(__('-- No Theme --'));
            $element = new \Magento\Data\Form\Element\Select();
            $element
                ->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setValues($options);
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

}
