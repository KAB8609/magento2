<?php 
/**
 * Newsletter subscribe controller 
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
 class Mage_Newsletter_IndexController extends Mage_Core_Controller_Front_Action 
 {
    public function indexAction() {
        $collection = Mage::getModel('newsletter/queue_collection')
            ->addOnlyForSendingFilter()
            ->load();
    }
 }