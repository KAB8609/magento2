<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml config system template source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Email_Template extends Varien_Object
{
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('Mage_Core_Model_Resource_Email_Template_Collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Default Template');
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = Mage::app()->getConfig()->getNode(
            Mage_Core_Model_Email_Template::XML_PATH_TEMPLATE_EMAIL . '/' . $nodeName . '/label'
        );
        if ($templateLabelNode) {
            $module = (string)$templateLabelNode->getParent()->getAttribute('module');
            $templateName = Mage::helper($module)->__((string)$templateLabelNode);
            $templateName = Mage::helper('Mage_Adminhtml_Helper_Data')->__('%s (Default)', $templateName);
        }
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
        return $options;
    }

}
