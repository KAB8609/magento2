<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Status_Grid_Column_State extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * @var Mage_Sales_Model_Order_Config
     */
    protected $_config;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Sales_Model_Order_Config $config
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Sales_Model_Order_Config $config,
        array $data = array()
    ) {
        parent::__construct ($context, $data);

        $this->_config = $config;
    }

    /**
     * Add decorated status to column
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateState');
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param Mage_Sales_Model_Order_Status $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateState($value, $row, $column, $isExport)
    {
        if ($value) {
            $cell = $value . '[' . $this->_config->getStateLabel($value) . ']';
        } else {
            $cell = $value;
        }
        return $cell;
    }
}