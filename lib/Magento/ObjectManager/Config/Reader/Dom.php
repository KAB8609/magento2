<?php
/**
 * ObjectManager DOM configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ObjectManager\Config\Reader;

class Dom extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/preference'         => 'for',
        '/config/type'               => 'name',
        '/config/type/param'         => 'name',
        '/config/type/plugin'        => 'name',
        '/config/virtualType'        => 'name',
        '/config/virtualType/param'  => 'name',
        '/config/virtualType/plugin' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\ObjectManager\Config\Mapper\Dom $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $filename
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\ObjectManager\Config\Mapper\Dom $converter,
        \Magento\Config\ValidationStateInterface $validationState,
        $filename = 'di.xml',
        $domDocumentClass = '\Magento\Config\Dom'
    ) {
        parent::__construct($fileResolver, $converter, $filename, $this->_idAttributes,
            $this->getSchemaFile(), '', $validationState->isValidated(), $domDocumentClass);
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return realpath(__DIR__ . '/../../etc/') . DIRECTORY_SEPARATOR . 'config.xsd';
    }
}
