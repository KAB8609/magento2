<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files tree block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Tree extends Magento_Backend_Block_Template
{
    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/treeJson', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
    }

    /**
     * Get tree json
     *
     * @param array $data
     * @return string
     */
    public function getTreeJson($data)
    {
        return Zend_Json::encode($data);
    }

    /**
     * Get root node name of tree
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->__('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = '/root';
        $path = $this->helper('Mage_Theme_Helper_Storage')->getSession()->getCurrentPath();
        if ($path) {
            $path = str_replace($this->helper('Mage_Theme_Helper_Storage')->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DIRECTORY_SEPARATOR, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DIRECTORY_SEPARATOR . $dirName;
                    $treePath .= '/' . $this->helper('Mage_Theme_Helper_Storage')->urlEncode($relative);
                }
            }
        }
        return $treePath;
    }
}
