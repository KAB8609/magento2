<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Block_Tax_Rate_ImportExport extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'tax/importExport.phtml';

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Mage_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }


}
