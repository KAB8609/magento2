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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Messages index controller
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_GiftmessageController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        /* Do nothing */
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout('popup');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/giftmessage_edit', 'giftmessage.edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        if($this->getRequest()->getParam('message')) {
            $giftMessage->load($this->getRequest()->getParam('message'));
        }
        try {
            $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getRequest()->getParam('type')));

            $giftMessage->setSender($this->getRequest()->getParam('sender'))
                ->setRecipient($this->getRequest()->getParam('recipient'))
                ->setMessage($this->getRequest()->getParam('messagetext'))
                ->save();


            $entity->load($this->getRequest()->getParam('item'))
                ->setGiftMessageId($giftMessage->getId())
                ->save();

            $this->getRequest()->setParam('message', $giftMessage->getId());
            $this->getRequest()->setParam('entity', $entity);
        } catch (Exception $e) {

        }

        $this->loadLayout('popup');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/giftmessage_edit', 'giftmessage.edit')->setSaveMode('save'));
        $this->renderLayout();
    }

    public function removeAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        try {
            $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getRequest()->getParam('type')));

            $entity->load($this->getRequest()->getParam('item'));
            if($entity->getGiftMessageId()) {
                $giftMessage->load($entity->getGiftMessageId());
                $giftMessage->delete();
                $entity->setGiftMessageId(0);
                $entity->save();
            }

            $this->getRequest()->setParam('message', null);
            $this->getRequest()->setParam('entity', $entity);
        } catch (Exception $e) {

        }

        $this->loadLayout('popup');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/giftmessage_edit', 'giftmessage.edit')->setSaveMode('remove'));
        $this->renderLayout();
    }

    protected function _getMappedType($type)
    {
        $map = array(
            'main'          =>  'quote',
            'item'          =>  'quote_item',
            'address'       =>  'quote_address',
            'address_item'  =>  'quote_address_item',
            'order'         =>  'order',
            'order_item'    =>  'order_item'
        );

        if (isset($map[$type])) {
            return $map[$type];
        }

        return null;
    }

    public function buttonAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getRequest()->getParam('type')));
        $entity->load($this->getRequest()->getParam('item'));
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/giftmessage_helper')
                                        ->setEntity($entity)
                                        ->setType($this->getRequest()->getParam('type'))->toHtml());
    }

} // Class Mage_GiftMessage_IndexController End