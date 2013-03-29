<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    view
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Lightweight theme that implements minimal required interface
 */
class Generator_ThemeLight extends Varien_Object implements Mage_Core_Model_ThemeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->getData('area');
    }

    /**
     * {@inheritdoc}
     */
    public function getThemePath()
    {
        return $this->getData('theme_path');
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPath()
    {
        return $this->getArea() . Mage_Core_Model_Theme::PATH_SEPARATOR . $this->getThemePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentTheme()
    {
        return $this->getData('parent_theme');
    }
}
