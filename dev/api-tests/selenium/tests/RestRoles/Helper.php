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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestRoles_Helper extends Mage_Selenium_TestCase
{
    /**
     *
     * @param srting $tab
     * @return bool 
     */
     public function tabIsPresent($tab)
    {
        return $this->controlIsPresent('tab', $tab);
    }
    
    /**
     *
     * @param string $restRoleData 
     */
    public function createRestRole($restRoleData)
    {
        $this->clickButton('add_new_rest_role');
        $this->fillForm($restRoleData, 'rest_role_info');
        $this->fillForm($restRoleData, 'rest_role_resources');
        $this->addParameter('rest_role_id', '0');
        $this->saveForm('save_role');
    }
    
     /**
     * Method finds xPath of searchable element from UIMap
     * @param string $tabName
     * @param string $fieldsetsName
     * @param string $fiedType
     * @param string $fieldName
     * @return string 
     */
    public function getUIMapFieldXpath($tabName, $fieldsetsName, $fiedType, $fieldName)
    {
        $UIMap = $this->getCurrentUimapPage()->getMainForm()->getElements();
        $tab = $UIMap['tabs']->getTab($tabName)->getElements();
        $restRoleInformation = $tab['fieldsets']->getFieldset($fieldsetsName)->getElements();
        return $restRoleInformation[$fiedType]->get($fieldName);
    }
    
     /**
     * Method finds field value using xPath from UIMap
     * @param string $tabName
     * @param string $fieldsetsName
     * @param string $fiedType
     * @param string $fieldName
     * @return string 
     */
    public function getFieldValue($tabName, $fieldsetsName, $fiedType, $fieldName)
    {
        $xpath = $this->getUIMapFieldXpath($tabName, $fieldsetsName, $fiedType, $fieldName);
        return $this->getElementByXpath($xpath, 'value');
    }
    
     /**
     * Method opens REST Role by name.
     * Example usage, $this->restRolesHelper()->openRestRoleByName('RoleName'),
     *
     * @param string $restRoleName
     */
    public function openRestRoleByName($restRoleName)
    {
        //browse to REST Rolese page
        $this->navigate('manage_rest_roles');
        $this->addParameter('rest_role_id', '0');
        //load search REST role template with empty fields and set Name
        $searchData = $this->loadData('search_rest_role', array('role_name' => $restRoleName));
        //open REST Role
        $this->assertTrue($this->searchAndOpen($searchData, true, 'rest_role_list'), 'REST Role is not found');
    }
    
     /**
     * Method deletes REST Role by name.
     * Example usage, $this->restRolesHelper()->openRestRoleByName('RoleName'),
     *
     * @param string $restRoleName
     */
    public function deleteRestRole($restRoleName)
    {
        //open REST Role by Name
        $this->openRestRoleByName($restRoleName);
        //click Delete Role button
        $this->clickButtonAndConfirm('delete_role', 'confirmation_for_delete');
    }
    
     /**
     *
     * Method checkes if the specific item of specific Grid is checked
     * returns FALSE if item isn't checked or not found
     * 
     * @return bool
     * @param array $gridData
     * @param array $gridName
     */
    public function isGridItemChecked($gridData, $gridName)
    {
        $gridItemXpath = $this->search($gridData, $gridName);
        $isChecked = false;
        if ($gridData != null) {
            $data = $gridItemXpath . "//input[@checked='checked']";
            $isChecked = $this->isElementPresent($data);
        }
        
        return $isChecked;
     }
     
     /**
     * Method provide edition REST Role by name.
     * @param string $restRoleName
     * @param array $newRestRoleData 
     */
    public function editRestRole($restRoleName, array $newRestRoleData)
    {
        //Open edited role
        $this->openRestRoleByName($restRoleName);
        //Fill all data
        $this->fillForm($newRestRoleData, 'rest_role_info');
        $this->fillForm($newRestRoleData, 'rest_role_resources');
        $this->saveForm('save_role');
    }
    
     /**
     * Method finds dropdown field text using xPath from UIMap
     * Implemented only for fields where param text insted value (dropdowns)
     * @param string $tabName
     * @param string $fieldsetsName
     * @param string $fiedType
     * @param string $fieldName
     * @return string 
     */
    public function getFieldText($tabName, $fieldsetsName, $fiedType, $fieldName)
    {
        $xpath = $this->getUIMapFieldXpath($tabName, $fieldsetsName, $fiedType, $fieldName) . '//option[@selected]';
        return $this->getElementByXpath($xpath, 'text');
    }
}
