<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Drawer Block
 *
 * @method Mage_Launcher_Model_Tile getTile()
 * @method Mage_Launcher_Block_Adminhtml_Drawer setTile(Mage_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Drawer extends Mage_Backend_Block_Widget_Form
{
    /**
     * Path to template file
     *
     * @todo Default template specified, but it should be changed to custom one
     * @var string
     */
    protected $_template = 'Mage_Backend::widget/form.phtml';

    /**
     * Get Tile Code
     *
     * @return string
     */
    public function getTileCode()
    {
        return $this->getTile()->getCode();
    }

    /**
     * Get Tile State
     *
     * @return int
     */
    public function getTileState()
    {
        return $this->getTile()->getState();
    }
}
