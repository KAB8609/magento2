<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Tax_Model_System_Config_Source_Tax_Country extends Mage_Directory_Model_Config_Source_Country
{
    protected $_options;

    public function toOptionArray($noEmpty=false)
    {
        $options = parent::toOptionArray($noEmpty);

        if(!$noEmpty) {
            if ($options) {
                $options[0]['label'] = Mage::helper('Mage_Tax_Helper_Data')->__('None');
            } else {
                $options = array(array('value'=>'', 'label'=>$this->helper('Mage_Tax_Helper_Data')->__('None')));
            }
        }

        return $options;
    }
}
