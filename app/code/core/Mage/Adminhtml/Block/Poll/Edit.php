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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll';

        $this->_updateButton('save', 'label', Mage::helper('poll')->__('Save Poll'));
        $this->_updateButton('delete', 'label', Mage::helper('poll')->__('Delete Poll'));

        $this->setValidationUrl(Mage::getUrl('*/*/validate', array('id' => $this->getRequest()->getParam($this->_objectId))));
        if( $this->getRequest()->getParam($this->_objectId) ) {
            $pollData = Mage::getModel('poll/poll')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('poll_data', $pollData);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('poll_data') && Mage::registry('poll_data')->getId() ) {
            return Mage::helper('poll')->__("Edit Poll '%s'", Mage::registry('poll_data')->getPollTitle());
        } else {
            return Mage::helper('poll')->__('New Poll');
        }
    }
}
