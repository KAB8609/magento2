<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Payment_Block_Form_Checkmo extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mage_Payment::form/checkmo.phtml');
    }

}
