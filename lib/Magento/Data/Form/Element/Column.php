<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form column
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Column extends Magento_Data_Form_Element_Abstract 
{
    public function __construct(Magento_Data_Form_ElementFactory $elementFactory, $attributes = array())
    {
        parent::__construct($elementFactory, $attributes);
        $this->setType('column');
    }
}