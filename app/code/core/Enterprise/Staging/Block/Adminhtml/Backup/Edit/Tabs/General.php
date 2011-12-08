<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging backup general info tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object
     */
    protected $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFieldNameSuffix('staging_backup');
    }

    /**
     * Prepare form fieldset and form values
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('staging_backup_general_fieldset',
            array('legend' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Backup Main Information')));

        $fieldset->addField('name', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Backup Name'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Backup Name'),
            'value'     => $this->getBackupName()
        ));

        $fieldset->addField('staging_name', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Website'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Website'),
            'value'     => $this->getStagingWebsiteName()
        ));

        $fieldset->addField('master_website', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Master Website'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Master Website'),
            'value'     => $this->getMasterWebsiteName()
        ));

        $fieldset->addField('backupCreateAt', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Created Date'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Created Date'),
            'value'     => $this->formatDate($this->getBackup()->getCreatedAt(), 'medium', true)
        ));

        $fieldset->addField('tablePrefix', 'label', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Table Prefix'),
            'title'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Table Prefix'),
            'value'     => $this->getBackup()->getStagingTablePrefix()
        ));

        $form->setFieldNameSuffix($this->getFieldNameSuffix());

        $this->setForm($form);

        return parent::_prepareForm();
    }


    /**
     * Retrieve master website name (if website exists)
     *
     * @return string
     */
    public function getMasterWebsiteName()
    {
        return $this->_getWebsiteName($this->getBackup()->getMasterWebsiteId());
    }

    /**
     * Retrieve staging website name (if website exists)
     *
     * @return string
     */
    public function getStagingWebsiteName()
    {
        return $this->_getWebsiteName($this->getBackup()->getStagingWebsiteId());
    }

    /**
     * Custom getter of website name by specified website Id
     *
     * @param int $websiteId
     * @return string
     */
    protected function _getWebsiteName($websiteId)
    {
        if ($websiteId) {
            $website = Mage::app()->getWebsite($websiteId);
            if ($website) {
                return $website->getName();
            }
        }
        return Mage::helper('Enterprise_Staging_Helper_Data')->__('No information');
    }

    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }

    /**
     * Backup name getter
     *
     * @return string
     */
    public function getBackupName()
    {
        return $this->getBackup()->getName();
    }
}
