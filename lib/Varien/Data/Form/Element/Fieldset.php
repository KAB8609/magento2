<?php
/**
 * Form fieldset
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Fieldset extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->_initElementsCollection();
        $this->setType('fieldset');
    }
    
    public function toHtml()
    {
        $html = '<fieldset id="'.$this->getHtmlId().'"'.$this->serialize(array('class')).'>'."\n";
        if ($this->getLegend()) {
            $html.= '<legend>'.$this->getLegend().'</legend>'."\n";
        }
        foreach ($this->getElements() as $element) {
        	$html.= $element->toHtml();
        }
        $html.= '</fieldset>'."\n";
        return $html;
    }
}