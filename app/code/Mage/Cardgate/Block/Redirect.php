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
 * Cardgate Redirect Block
 *
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * Registry
     *
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * Gateway Factory
     *
     * @var Mage_Cardgate_Model_Gateway_Factory
     */
    protected $_gatewayFactory;

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
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Cardgate_Model_Gateway_Factory $gatewayFactory
     * @param Varien_Data_FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Registry $registry,
        Mage_Cardgate_Model_Gateway_Factory $gatewayFactory,
        Varien_Data_FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_registry = $registry;
        $this->_gatewayFactory = $gatewayFactory;
        $this->_formFactory = $formFactory;
    }

    /**
     * Returns Form HTML
     *
     * @return string
     */
    public function getForm()
    {
        $modelName = $this->_registry->registry('cardgate_model');
        $model = $this->_gatewayFactory->create($modelName);
        $this->_registry->unregister('cardgate_model');

        /** @var Varien_Data_Form $order */
        $form = $this->_formFactory->create();
        $form->setAction($model->getGatewayUrl())
            ->setId('cardgateplus_checkout')
            ->setName('cardgateplus_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($model->getCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        return $form->getHtml();
    }
}