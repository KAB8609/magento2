<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface needed to be implemented by controller that wants to
 * show product view page
 */
interface Mage_Catalog_Controller_Product_View_Interface
{
    /**
     * Loads layout messages from message storage
     *
     * @param string $messagesStorage
     */
    public function initLayoutMessages($messagesStorage);
}
