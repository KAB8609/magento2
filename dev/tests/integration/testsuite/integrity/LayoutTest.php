<?php
/**
 * Layout nodes integrity tests
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $package
     * @param string $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandleHierarchy($area, $package, $theme)
    {
        $layoutUpdate = new Mage_Core_Model_Layout_Update(array(
            'area' => $area, 'package' => $package, 'theme' => $theme
        ));
        $xml = $layoutUpdate->getFileLayoutUpdatesXml();
        $handles = $xml->xpath('/layouts/*[@parent]') ?: array();
        /** @var Mage_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            $parent = $node->getAttribute('parent');
            if (!$xml->xpath("/layouts/{$parent}")) {
                $errors[$node->getName()] = $parent;
            }
        }
        if ($errors) {
            $this->fail("Reference(s) to non-existing parent handle found at:\n" . var_export($errors, 1));
        }
    }

    /**
     * List all themes available in the system
     *
     * The "no theme" (false) is prepended to the result -- it means layout updates must be loaded from modules
     *
     * A test that uses such data provider is supposed to gather view resources in provided scope
     * and analyze their integrity. For example, merge and verify all layouts in this scope.
     *
     * Such tests allow to uncover complicated code integrity issues, that may emerge due to view fallback mechanism.
     * For example, a module layout file is overlapped by theme layout, which has mistakes.
     * Such mistakes can be uncovered only when to emulate this particular theme.
     * Also emulating "no theme" mode allows to detect inversed errors: when there is a view file with mistake
     * in a module, but it is overlapped by every single theme by files without mistake. Putting question of code
     * duplication aside, it is even more important to detect such errors, than an error in a single theme.
     *
     * @return array
     */
    public function areasAndThemesDataProvider()
    {
        $result = array();
        foreach (array('adminhtml', 'frontend', 'install') as $area) {
            $result[] = array($area, false, false);
            foreach (Mage::getDesign()->getDesignEntitiesStructure($area, false) as $package => $themes) {
                foreach (array_keys($themes) as $theme) {
                    $result[] = array($area, $package, $theme);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $area
     * @param string $package
     * @param string $theme
     * @dataProvider areasAndThemesDataProvider
     */
    public function testHandleLabels($area, $package, $theme)
    {
        $layoutUpdate = new Mage_Core_Model_Layout_Update(array(
            'area' => $area, 'package' => $package, 'theme' => $theme
        ));
        $xml = $layoutUpdate->getFileLayoutUpdatesXml();
        $handles = $xml->xpath('/layouts/*[@type="page" or @translate="label"]') ?: array();
        /** @var Mage_Core_Model_Layout_Element $node */
        $errors = array();
        foreach ($handles as $node) {
            if (!$node->xpath('label')) {
                $errors[] = $node->getName();
            }
        }
        if ($errors) {
            $this->fail("The following handles are declared as page types or claim to have label,"
                . " but they don't have a label:\n" . var_export($errors, 1)
            );
        }
    }
}
