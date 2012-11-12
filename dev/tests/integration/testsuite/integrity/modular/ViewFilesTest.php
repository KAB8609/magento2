<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_Modular_ViewFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $application
     * @param string $file
     * @dataProvider viewFilesFromModulesViewDataProvider
     */
    public function testViewFilesFromModulesView($application, $file)
    {
        Mage::getDesign()->setArea($application);
        $params = $application == 'frontend' ? array('theme' => false) : array();
        $this->assertFileExists(Mage::getDesign()->getViewFile($file, $params));
    }

    /**
     * Collect getViewUrl() calls from base templates
     *
     * @return array
     */
    public function viewFilesFromModulesViewDataProvider()
    {
        $files = array();
        foreach ($this->_getEnabledModules() as $moduleName) {
            $moduleViewDir = Mage::getConfig()->getModuleDir('view', $moduleName);
            if (!is_dir($moduleViewDir)) {
                continue;
            }
            $this->_findViewFilesInViewFolder($moduleViewDir, $files);
        }
        $result = array();
        foreach ($files as $area => $references) {
            foreach ($references as $file) {
                $result[] = array($area, $file);
            }
        }
        return $result;
    }

    /**
     * Find view file references per area in declared modules.
     *
     * @param string $moduleViewDir
     * @param array $files
     * @return null
     */
    protected function _findViewFilesInViewFolder($moduleViewDir, &$files)
    {
        foreach (new DirectoryIterator($moduleViewDir) as $viewAppDir) {
            $area = $viewAppDir->getFilename();
            if (0 === strpos($area, '.') || !$viewAppDir->isDir()) {
                continue;
            }
            foreach (new RecursiveIteratorIterator(
                         new RecursiveDirectoryIterator($viewAppDir->getRealPath())) as $fileInfo
            ) {
                $references = $this->_findReferencesToViewFile($fileInfo);
                if (!isset($files[$area])) {
                    $files[$area] = $references;
                } else {
                    $files[$area] = array_merge($files[$area], $references);
                }
                $files[$area] = array_unique($files[$area]);
            }
        }
    }

    /**
     * Scan specified file for getViewUrl() pattern
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToViewFile(SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isFile() || !preg_match('/\.phtml$/', $fileInfo->getFilename())) {
            return array();
        }

        $result = array();
        $content = file_get_contents($fileInfo->getRealPath());
        if (preg_match_all('/\$this->getViewFileUrl\(\'([^\']+?)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $value) {
                if ($this->_isFileForDisabledModule($value)) {
                    continue;
                }
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * getViewUrl() hard-coded in the php-files
     *
     * @param string $application
     * @param string $file
     * @dataProvider viewFilesFromModulesCodeDataProvider
     */
    public function testViewFilesFromModulesCode($application, $file)
    {
        Mage::getDesign()->setArea($application);
        $this->assertFileExists(Mage::getDesign()->getViewFile($file));
    }

    /**
     * @return array
     */
    public function viewFilesFromModulesCodeDataProvider()
    {
        // All possible files to test
        $allFiles = array(
            array('frontend', 'Enterprise_Reward::images/payment.gif'),
            array('frontend', 'Enterprise_Reward::images/my_account.gif'),
            array('adminhtml', 'images/ajax-loader.gif'),
            array('adminhtml', 'Mage_Adminhtml::images/error_msg_icon.gif'),
            array('adminhtml', 'images/fam_bullet_disk.gif'),
            array('adminhtml', 'Mage_Adminhtml::images/fam_bullet_success.gif'),
            array('adminhtml', 'images/fam_link.gif'),
            array('adminhtml', 'images/grid-cal.gif'),
            array('adminhtml', 'images/rule_chooser_trigger.gif'),
            array('adminhtml', 'images/rule_component_add.gif'),
            array('adminhtml', 'images/rule_component_apply.gif'),
            array('adminhtml', 'images/rule_component_remove.gif'),
            array('adminhtml', 'Mage_Cms::images/placeholder_thumbnail.jpg'),
            array('adminhtml', 'Mage_Cms::images/wysiwyg_skin_image.png'),
            array('adminhtml', 'Mage_Core::fam_book_open.png'),
            array('adminhtml', 'Mage_Page::favicon.ico'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_account.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_account_android.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_account_ipad.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_cart.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_cart_android.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_home.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_home_android.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_info_android.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_more.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_page.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_search.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_search_android.png'),
            array('adminhtml', 'Mage_XmlConnect::images/tab_shop_android.png'),
            array('frontend', 'Mage_Cms::images/about_us_img.jpg'),
            array('frontend', 'Mage_Core::calendar.gif'),
            array('frontend', 'Mage_Core::fam_book_open.png'),
            array('frontend', 'Mage_Page::favicon.ico'),
            array('frontend', 'Mage_Catalog::images/product/placeholder/image.jpg'),
            array('frontend', 'Mage_Catalog::images/product/placeholder/small_image.jpg'),
            array('install',  'Mage_Page::favicon.ico'),
        );

        return $this->_removeDisabledModulesFiles($allFiles);
    }

    /**
     * Scans array of file information and removes files, that belong to disabled modules.
     * Thus we won't test them.
     *
     * @param array $allFiles
     * @return array
     */
    protected function _removeDisabledModulesFiles($allFiles)
    {
        $result = array();
        foreach ($allFiles as $fileInfo) {
            $fileName = $fileInfo[1];
            if ($this->_isFileForDisabledModule($fileName)) {
                continue;
            }
            $result[] = $fileInfo;
        }
        return $result;
    }
}
