<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme customization service class for quick styles
 */
class Mage_DesignEditor_Model_Theme_Customization_File_QuickStyleCss
    extends Mage_Core_Model_Theme_Customization_FileAbstract
{
    /**#@+
     * QuickStyles CSS file type customization
     */
    const TYPE = 'quick_style_css';
    const CONTENT_TYPE = 'css';
    /**#@-*/

    /**
     * Default filename
     */
    const FILE_NAME = 'quick_style.css';

    /**
     * Default order position
     */
    const SORT_ORDER = 20;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    protected  function _prepareFileName(Mage_Core_Model_Theme_FileInterface $file)
    {
        $file->setFileName(self::FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareSortOrder(Mage_Core_Model_Theme_FileInterface $file)
    {
        $file->setData('sort_order', self::SORT_ORDER);
    }
}