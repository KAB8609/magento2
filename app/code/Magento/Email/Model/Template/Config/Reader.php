<?php
/**
 * Loads email template configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @var \Magento\Module\Dir\ReverseResolver
     */
    private $_moduleDirResolver;

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Email\Model\Template\Config\Converter $converter
     * @param \Magento\Email\Model\Template\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\Module\Dir\ReverseResolver $moduleDirResolver
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Email\Model\Template\Config\Converter $converter,
        \Magento\Email\Model\Template\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        \Magento\Module\Dir\ReverseResolver $moduleDirResolver
    ) {
        $fileName = 'email_templates.xml';
        $idAttributes = array(
            '/config/template' => 'id',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
        $this->_moduleDirResolver = $moduleDirResolver;
    }

    /**
     * Add information on context of a module, config file belongs to
     *
     * {@inheritdoc}
     * @throws \UnexpectedValueException
     */
    protected function _readFileContents($filename)
    {
        $result = parent::_readFileContents($filename);
        $moduleName = $this->_moduleDirResolver->getModuleName($filename);
        if (!$moduleName) {
            throw new \UnexpectedValueException("Unable to determine a module, file '$filename' belongs to.");
        }
        $result = str_replace('<template ', '<template module="' . $moduleName . '" ', $result);
        return $result;
    }
}