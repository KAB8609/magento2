<?php
/**
 * Factory is used to hide the details of how a Twig Environment is built.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_Twig_EnvironmentFactory
{
    /**
     * @var Mage_Core_Model_TemplateEngine_Twig_Extension
     */
    protected $_extension;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;
    
    /**
     * @var Twig_Environment
     */
    private $_environment;

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dir;
    
    /**
     * @var Mage_Core_Model_Logger
     */
    private $_logger;

    /**
     * @var Twig_LoaderInterface
     */
    private $_loader;

    /**
     * Create new instance of factory
     *
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_TemplateEngine_Twig_Extension $extension
     * @param Mage_Core_Model_Dir $dir
     * @param Mage_Core_Model_Logger $logger
     * @param Twig_LoaderInterface $loader
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_TemplateEngine_Twig_Extension $extension,
        Mage_Core_Model_Dir $dir,
        Mage_Core_Model_Logger $logger,
        Twig_LoaderInterface $loader
    ) {
        $this->_filesystem = $filesystem;
        $this->_extension = $extension;
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->_loader = $loader;
        $this->_environment = null;
    }

    /**
     * Initialize (if necessary) and return the Twig environment.
     *
     * @return Twig_Environment
     */
    public function create()
    {
        if ($this->_environment === null) {
            $this->_environment = new Twig_Environment($this->_loader);
            try {
                $precompiledTmpltDir = $this->_dir->getDir(Mage_Core_Model_Dir::VAR_DIR) . '/twig_templates';
                $this->_filesystem->createDirectory($precompiledTmpltDir);
                $this->_environment->setCache($precompiledTmpltDir);
            } catch (Magento_Filesystem_Exception $e) {
                // Twig will just run slowly but not worth stopping Magento for it
                $this->_logger->logException($e);
            } catch (InvalidArgumentException $e) {
                // Can happen if path isn't found, shouldn't stop Magento
                $this->_logger->logException($e);
            }
            $this->_environment->enableStrictVariables();
            $this->_environment->addExtension(new Twig_Extension_Escaper('html'));
            $this->_environment->addExtension(new Twig_Extension_Optimizer(1));
            $this->_environment->addExtension($this->_extension);
            $this->_environmentInitialized = true;
        }
        return $this->_environment;
    }
}
