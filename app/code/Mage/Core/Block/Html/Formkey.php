<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML input element block
 */
class Mage_Core_Block_Html_Formkey extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Model_Session
     */
    protected $_sessionModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Session $sessionModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Session $sessionModel,
        array $data = array()
    ) {
        $this->_sessionModel = $sessionModel;
        parent::__construct($context, $data);
    }

    /**
     * Get session model
     *
     * @return Mage_Core_Model_Session
     */
    public function getSessionModel()
    {
        return $this->_sessionModel;
    }
}
