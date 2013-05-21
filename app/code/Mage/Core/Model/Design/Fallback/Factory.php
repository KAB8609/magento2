<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces all sorts of fallback rules
 */
class Mage_Core_Model_Design_Fallback_Factory
{
    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(Mage_Core_Model_Dir $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Retrieve newly created fallback rule for locale files, such as CSV translation maps
     *
     * @return Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createLocaleFileRule()
    {
        $themesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        return new Mage_Core_Model_Design_Fallback_Rule_Theme(
            new Mage_Core_Model_Design_Fallback_Rule_Simple("$themesDir/<area>/<theme_path>/locale/<locale>")
        );
    }

    /**
     * Retrieve newly created fallback rule for dynamic view files, such as layouts and templates
     *
     * @return Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createFileRule()
    {
        $themesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES);
        return new Mage_Core_Model_Design_Fallback_Rule_ModularSwitch(
            new Mage_Core_Model_Design_Fallback_Rule_Theme(
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$themesDir/<area>/<theme_path>"
                )
            ),
            new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                new Mage_Core_Model_Design_Fallback_Rule_Theme(
                    new Mage_Core_Model_Design_Fallback_Rule_Simple(
                        "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                    )
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }

    /**
     * Retrieve newly created fallback rule for static view files, such as CSS, JavaScript, images, etc.
     *
     * @return Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function createViewFileRule()
    {
        $themesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $modulesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MODULES);
        $pubLibDir = $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_LIB);
        return new Mage_Core_Model_Design_Fallback_Rule_ModularSwitch(
            new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                new Mage_Core_Model_Design_Fallback_Rule_Theme(
                    new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/locale/<locale>", array('locale')
                        ),
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>"
                        ),
                    ))
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple($pubLibDir),
            )),
            new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                new Mage_Core_Model_Design_Fallback_Rule_Theme(
                    new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/locale/<locale>/<namespace>_<module>", array('locale')
                        ),
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                        ),
                    ))
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>/locale/<locale>", array('locale')
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }
}
