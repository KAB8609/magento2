<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Install config
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Config extends Varien_Simplexml_Config
{

    /**
     * @var Mage_Core_Model_Config_StorageInterface
     */
    protected $_configStorage;

    const XML_PATH_WIZARD_STEPS     = 'wizard/steps';
    const XML_PATH_CHECK_WRITEABLE  = 'check/filesystem/writeable';
    const XML_PATH_CHECK_EXTENSIONS = 'check/php/extensions';

    public function __construct(Mage_Core_Model_Config_StorageInterface $configStorage)
    {
        parent::__construct();
        $this->loadString('<?xml version="1.0"?><config></config>');
        $configStorage->loadModulesConfiguration('install.xml', $this);
    }

    /**
     * Get array of wizard steps
     *
     * array($inndex => Varien_Object )
     *
     * @return array
     */
    public function getWizardSteps()
    {
        $steps = array();
        foreach ((array)$this->getNode(self::XML_PATH_WIZARD_STEPS) as $stepName => $step) {
            $stepObject = new Varien_Object((array)$step);
            $stepObject->setName($stepName);
            $steps[] = $stepObject;
        }
        return $steps;
    }

    /**
     * Retrieve writable path for checking
     *
     * array(
     *      ['writeable'] => array(
     *          [$index] => array(
     *              ['path']
     *              ['recursive']
     *          )
     *      )
     * )
     *
     * @deprecated since 1.7.1.0
     *
     * @return array
     */
    public function getPathForCheck()
    {
        $res = array();

        $items = (array) $this->getNode(self::XML_PATH_CHECK_WRITEABLE);

        foreach ($items as $item) {
            $res['writeable'][] = (array) $item;
        }

        return $res;
    }

    /**
     * Retrieve writable full paths for checking
     *
     * @return array
     */
    public function getWritableFullPathsForCheck()
    {
        $paths = array();
        $items = (array) $this->getNode(self::XML_PATH_CHECK_WRITEABLE);
        foreach ($items as $nodeKey => $item) {
            $value = (array)$item;
            $value['path'] = Mage::getBaseDir($nodeKey);
            $paths[$nodeKey] = $value;
        }

        return $paths;
    }

    /**
     * Retrieve required PHP extensions
     *
     * @return array
     */
    public function getExtensionsForCheck()
    {
        $res = array();
        $items = (array) $this->getNode(self::XML_PATH_CHECK_EXTENSIONS);

        foreach ($items as $name => $value) {
            if (!empty($value)) {
                $res[$name] = array();
                foreach ($value as $subname => $subvalue) {
                    $res[$name][] = $subname;
                }
            }
            else {
                $res[$name] = (array) $value;
            }
        }

        return $res;
    }

}
