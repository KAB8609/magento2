<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Radio_Extended
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Radio
{
    /**
     * Prepare data for renderer
     *
     * @return array
     */
    protected function _getValues()
    {
        return $this->getColumn()->getValues();
    }
}