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
namespace Magento\Tools\View\Generator;

class ThemeLight extends \Magento\Object implements \Magento\View\Design\Theme
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
        return $this->getArea() . \Magento\Core\Model\Theme::PATH_SEPARATOR . $this->getThemePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentTheme()
    {
        return $this->getData('parent_theme');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return (string)$this->getData('code');
    }

    /**
     * {@inheritdoc}
     */
    public function isPhysical()
    {
        return false;
    }
}
