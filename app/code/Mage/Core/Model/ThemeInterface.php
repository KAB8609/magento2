<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minimal required interface a theme has to implement
 */
interface Mage_Core_Model_ThemeInterface
{
    /**
     * Retrieve code of an area a theme belongs to
     *
     * @return string
     */
    public function getArea();

    /**
     * Retrieve theme path unique within an area
     *
     * @return string
     */
    public function getThemePath();

    /**
     * Retrieve theme path unique across areas
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Retrieve parent theme instance
     *
     * @return Mage_Core_Model_ThemeInterface|null
     */
    public function getParentTheme();
}