<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid widget column renderer massaction
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    public function renderHeader()
    {
        return '<input type="checkbox" onclick="'.$this->getColumn()->getGrid()->getMassactionBlock()->getJsObjectName().'.checkCheckboxes(this)" class="checkbox" title="'.$this->__('Select All').'"/>';
    }

    public function renderProperty()
    {
        $out = 'width="28" align="center"';
        return $out;
    }

    protected function _getCheckboxHtml($value, $checked)
    {
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" value="' . $value . '" class="massaction-checkbox"'.$checked.'/>';
    }

} // Class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Massaction End