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
 * Block class for rendering design editor tree of files
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Tree extends Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Tree
{
    /**
     * Override root node name of tree specific to design editor.
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->__('CSS Editor ') . $this->__($this->helper('Mage_Theme_Helper_Storage')->getStorageTypeName());
    }
}
