<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract toolbar block
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract extends Mage_Backend_Block_Template
{
    /**
     * Current VDE mode
     *
     * @var int
     */
    protected $_mode;

    /**
     * Get current VDE mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Get current VDE mode
     *
     * @param int $mode
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * Check if visual editor is in design mode
     *
     * @return bool
     */
    public function isDesignMode()
    {
        return $this->getMode() == Mage_DesignEditor_Model_State::MODE_DESIGN;
    }

    /**
     * Check if visual editor is in navigation mode
     *
     * @return bool
     */
    public function isNavigationMode()
    {
        return $this->getMode() == Mage_DesignEditor_Model_State::MODE_NAVIGATION;
    }
}
