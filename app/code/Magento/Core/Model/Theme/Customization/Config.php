<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * {@inheritdoc}
 */
namespace Magento\Core\Model\Theme\Customization;

class Config implements \Magento\View\Design\Theme\Customization\ConfigInterface
{
    /**
     * XML path to definitions of customization services
     */
    const XML_PATH_CUSTOM_FILES = 'theme/customization';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(\Magento\Core\Model\Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileTypes()
    {
        $types = array();
        $convertNode = $this->config->getValue(self::XML_PATH_CUSTOM_FILES, 'default');
        if ($convertNode) {
            foreach ($convertNode as $name => $value) {
                $types[$name] = $value;
            }
        }
        return $types;
    }
}