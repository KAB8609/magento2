<?php
/**
 * Image Validator Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Import_Image_Validator_Factory
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Validator_BuilderFactory
     */
    protected $_validatorBuilderFactory;

    /**
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Saas_ImportExport_Helper_Data $helper
     * @param Magento_Validator_BuilderFactory $validatorBuilderFactory
     */
    public function __construct(
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Saas_ImportExport_Helper_Data $helper,
        Magento_Validator_BuilderFactory $validatorBuilderFactory
    ) {
        $this->_configuration = $configuration;
        $this->_helper = $helper;
        $this->_validatorBuilderFactory = $validatorBuilderFactory;
    }

    /**
     * Create image validator
     *
     * @return Magento_Validator
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @see https://jira.corp.x.com/browse/MAGETWO-10439
     */
    public function createValidator()
    {
        $filenameLimit = $this->_configuration->getImageFilenameLimit();
        $allowedExtensions = $this->_configuration->getImageAllowedExtensions();
        $sizeLimit = $this->_configuration->getImageFileSizeLimit();
        $allowedMimetypes = $this->_configuration->getImageAllowedMimetypes();
        $widthLimit = $this->_configuration->getImageWidthLimit();
        $heightLimit = $this->_configuration->getImageHeightLimit();

        // @codingStandardsIgnoreStart
        $messageFilenameWrong = $this->_helper->__("File name error (only latin a-z, A-Z, 0-9, '-' and '_' symbols are allowed in files and folders names) in:");
        // @codingStandardsIgnoreEnd
        $messageFilenameLimit = 'File name is too long:';
        $extensionsString = "'" . implode("', '", array_values($allowedExtensions)) . "'";
        $messageWrongImage = $this->_helper->__('Unsupported image format (only %1 image file types are allowed) in:',
            $extensionsString);
        $messageFileSizeNotFound = $this->_helper->__('File error for:');
        $messageFileSizeTooBig = $this->_helper->__('File size is larger than %1 bytes in:', $sizeLimit);
        $dimensions = sprintf('%sx%s', $widthLimit, $heightLimit);
        $messageWrongImageSize = $this->_helper->__('Image dimensions are larger than %1 in:', $dimensions);

        // https://jira.corp.x.com/browse/MAGETWO-10439
        /** @var Magento_Validator_Builder $builder */
        $builder = $this->_validatorBuilderFactory->create(array(
            'constraints' => array(
                array(
                    'alias' => 'FileName',
                    'type' => '',
                    'class' => 'Saas_ImportExport_Model_Import_Image_Validator_FileName',
                    'options' => array(
                        'arguments' => array(
                            array('lengthLimit' => $filenameLimit, 'pattern' => '/^[a-z\d\-_\/\.]+$/i'),
                        ),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        Saas_ImportExport_Model_Import_Image_Validator_FileName::NAME_IS_WRONG
                                            => $messageFilenameWrong,
                                        Saas_ImportExport_Model_Import_Image_Validator_FileName::NAME_LENGTH_TOO_BIG
                                            => $messageFilenameLimit,
                                    ),
                                ),
                            ),
                        ),
                        'breakChainOnFailure' => true,
                    ),
                ),
                array(
                    'alias' => 'Extension',
                    'type' => '',
                    'class' => 'Magento_Validator_File_Extension',
                    'options' => array(
                        'arguments' => array($allowedExtensions),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        Magento_Validator_File_Extension::FALSE_EXTENSION => $messageWrongImage,
                                        Magento_Validator_File_Extension::NOT_FOUND => $messageWrongImage,
                                    ),
                                ),
                            ),
                        ),
                        'breakChainOnFailure' => true,
                    ),
                ),
                array(
                    'alias' => 'Size',
                    'type' => '',
                    'class' => 'Magento_Validator_File_Size',
                    'options' => array(
                        'arguments' => array($sizeLimit),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        Magento_Validator_File_Size::NOT_FOUND => $messageFileSizeNotFound,
                                        Magento_Validator_File_Size::TOO_BIG => $messageFileSizeTooBig,
                                    ),
                                ),
                            ),
                        ),
                        'breakChainOnFailure' => true,
                    ),
                ),
                array(
                    'alias' => 'IsImage',
                    'type' => '',
                    'class' => 'Magento_Validator_File_IsImage',
                    'options' => array(
                        'arguments' => array($allowedMimetypes),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        Magento_Validator_File_IsImage::FALSE_TYPE => $messageWrongImage,
                                        Magento_Validator_File_IsImage::NOT_DETECTED => $messageWrongImage,
                                        Magento_Validator_File_IsImage::NOT_READABLE => $messageWrongImage,
                                    ),
                                ),
                            ),
                        ),
                        'breakChainOnFailure' => true,
                    ),
                ),
                array(
                    'alias' => 'ImageSize',
                    'type' => '',
                    'class' => 'Magento_Validator_File_ImageSize',
                    'options' => array(
                        'arguments' => array(
                            array('maxwidth' => $widthLimit, 'maxheight' => $heightLimit),
                        ),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        Magento_Validator_File_ImageSize::WIDTH_TOO_BIG => $messageWrongImageSize,
                                        Magento_Validator_File_ImageSize::WIDTH_TOO_SMALL => $messageWrongImageSize,
                                        Magento_Validator_File_ImageSize::HEIGHT_TOO_BIG => $messageWrongImageSize,
                                        Magento_Validator_File_ImageSize::HEIGHT_TOO_SMALL => $messageWrongImageSize,
                                        Magento_Validator_File_ImageSize::NOT_DETECTED => $messageWrongImage,
                                        Magento_Validator_File_ImageSize::NOT_READABLE => $messageWrongImage,
                                    ),
                                ),
                            ),
                        ),
                        'breakChainOnFailure' => true,
                    ),
                ),
            ),
        ));

        return $builder->createValidator();
    }
}
