<?php
/**
 * The list of available authentication types
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_Authentication
{
    /** @var Mage_Core_Model_Translate $_translator */
    private $_translator;

    /** @var array $_authenticationTypes */
    private $_authenticationTypes;


    /**
     * @param array $authenticationTypes
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(array $authenticationTypes, Mage_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
        $this->_authenticationTypes = $authenticationTypes;
    }

    /**
     * Get available authentication types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_authenticationTypes;

    }

    /**
     * Return authentications for use by a form
     *
     * @return array
     */
    public function getAuthenticationsForForm()
    {
        $elements = array();
        foreach ($this->_authenticationTypes as $authName => $authentication) {
            $elements[] = array(
                'label' => $this->_translator->translate(array($authentication)),
                'value' => $authName,
            );
        }

        return $elements;
    }
}