<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Block_Form_Ideal extends Mage_Payment_Block_Form
{
    /**
     * Banks list
     *
     * @var array
     */
    protected $_banks = array();

    /**
     * Form Factory
     *
     * @var Varien_Data_FormFactory
     */
    protected $_formFactory;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Varien_Data_FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Varien_Data_FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_formFactory = $formFactory;
    }

    /**
     * Set template and init banks list
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('form/ideal.phtml');
        $this->_initBanksList();
    }

    /**
     * Return information payment object
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfoInstance()
    {
        return $this->getMethod()->getInfoInstance();
    }

    /**
     * Returns HTML options for select field with iDEAL banks
     *
     * @return string
     */
    public function getSelectField()
    {
        $selectOptions = array_merge(
            array('' => $this->__('--Please select--')),
            $this->_banks
        );

        $_code = $this->getMethodCode();

        /** @var Varien_Data_Form $order */
        $form =$this->_formFactory->create();
        $form->addField($_code . '_ideal_issuer', 'select', array(
            'name'      => 'payment[additional_information][ideal_issuer_id]',
            'class'     => 'input-text required-entry',
            'label'     => $this->__('Select your bank'),
            'values'    => $selectOptions,
            'required'  => true,
            'disabled'  => false,
        ));

        return $form->getHtml();
    }

    /**
     * Init banks list
     */
    protected function _initBanksList()
    {
        $this->_banks = array(
            '0021' => $this->__('Rabobank'),
            '0031' => $this->__('ABN Amro'),
            '0091' => $this->__('Friesland Bank'),
            '0721' => $this->__('ING'),
            '0751' => $this->__('SNS Bank'),
            '-'    => $this->__('------ Additional Banks ------'),
            '0161' => $this->__('Van Lanschot Bank'),
            '0511' => $this->__('Triodos Bank'),
            '0761' => $this->__('ASN Bank'),
            '0771' => $this->__('SNS Regio Bank'),
        );
    }
}