<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging event block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Event_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setFieldNameSuffix('event');
        $this->helper = Mage::helper('enterprise_staging');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset = $form->addFieldset('event_fieldset', array('legend'=>Mage::helper('enterprise_staging')->__('Staging Event Information')));

        $fieldset->addField('name', 'label', array(
            'label'     => $this->helper->__('Label'),
            'title'     => $this->helper->__('Label'),
            'name'      => 'name',
            'value'     => $this->getStaging()->getName()
        ));

        $fieldset->addField('code', 'label', array(
            'label'     => $this->helper->__('Staging Event Code'),
            'title'     => $this->helper->__('Staging Event Code'),
            'name'      => 'code',
            'value'     => $this->getEvent()->getCode()
        ));

        $fieldset->addField('state', 'label', array(
            'label'     => $this->helper->__('Staging Event State'),
            'title'     => $this->helper->__('Staging Event State'),
            'name'      => 'state',
            'value'     => $this->getEvent()->getSateLabel()
        ));

        $fieldset->addField('status', 'label', array(
            'label'     => $this->helper->__('Staging Event Status'),
            'title'     => $this->helper->__('Staging Event Status'),
            'name'      => 'status',
            'value'     => $this->getEvent()->getStatusLabel()
        ));

        $fieldset->addField('created_at', 'label', array(
            'label'     => $this->helper->__('Completed at'),
            'title'     => $this->helper->__('Completed at'),
            'name'      => 'created_at',
            'value'     => $this->getEvent()->getCreatedAt()
        ));

        $fieldset->addField('comment', 'textarea', array(
            'label'     => $this->helper->__('Comments'),
            'title'     => $this->helper->__('Comments'),
            'name'      => 'comment',
            'value'     => $this->getEvent()->getComment(),
            'readonly'  => true
        ));

        $fieldset->addField('log', 'textarea', array(
            'label'     => $this->helper->__('Log'),
            'title'     => $this->helper->__('Log'),
            'name'      => 'log',
            'value'     => $this->getEvent()->getLog(),
            'readonly'  => true
        ));

        $form->setFieldNameSuffix($this->getFieldNameSuffix());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Retrieve event object
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', Mage::registry('staging_event'));
        }
        return $this->getData('staging_event');
    }
}