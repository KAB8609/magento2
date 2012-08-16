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
 * Visual design editor layout model
 *
 * @method bool getRemoveFlag()
 * @method Varien_Simplexml_Element getHandleObject()
 * @method Mage_DesignEditor_Model_History_Manager_Adapter_Layout setHandleObject()
 * @method Mage_DesignEditor_Model_History_Manager_Adapter_Layout setRemoveFlag()
 * @method Mage_DesignEditor_Model_History_Manager_Adapter_Layout setActions()
 */
class Mage_DesignEditor_Model_History_Manager_Adapter_Layout
    extends Mage_DesignEditor_Model_History_Manager_Adapter_Abstract
{
    /**
     * Type add
     */
    const TYPE_ADD = 'Add';

    /**
     * Type render
     */
    const TYPE_RENDER = 'Render';

    /**
     * Action move
     */
    const ACTION_MOVE = 'move';

    /**
     * Action remove
     */
    const ACTION_REMOVE = 'remove';

    /**
     * Add action to element
     *
     * @param string $action
     * @param array $data
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Layout
     */
    public function addAction($action, $data)
    {
        $this->_executeActionByType($action, self::TYPE_ADD, $data);
        return $this;
    }

    /**
     * Execute action by type
     *
     * @throws Mage_DesignEditor_Exception
     * @param string $action
     * @param string $type
     * @param null|array $data
     */
    protected function _executeActionByType($action, $type, $data = null)
    {
        switch ($action) {
            case self::ACTION_MOVE:
                return $this->{'_' . self::ACTION_MOVE . $type}($data);
                break;
            case self::ACTION_REMOVE:
                return $this->{'_' . self::ACTION_REMOVE . $type}($data);
                break;
            default:
                throw new Mage_DesignEditor_Exception(
                    Mage::helper('Mage_DesignEditor_Helper_Data')->__('Action not exist: %s', $action)
                );
                break;
        }
    }

    /**
     * Remove action
     *
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Layout
     */
    protected function _removeAdd()
    {
        $this->_clearActions()->setRemoveFlag(true)->setActions(array(self::ACTION_REMOVE => array()));
        return $this;
    }

    /**
     * Clear actions data
     *
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Layout
     */
    protected function _clearActions()
    {
        $this->setActions(null);
        return $this;
    }

    /**
     * Action move
     *
     * @param array $data
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Layout
     */
    protected function _moveAdd($data)
    {
        if ($this->getRemoveFlag()) {
            return $this;
        }
        $this->setActions(array(self::ACTION_MOVE => $data));
        return $this;
    }

    /**
     * Element render action
     *
     * @return Mage_DesignEditor_Model_History_Manager_Adapter_Layout
     */
    public function render()
    {
        /** @var $handleObject Varien_Simplexml_Element */
        $handleObject = $this->getHandleObject();
        foreach ($this->getActions() as $action => $data) {
            $handleObject->appendChild($this->_executeActionByType($action, self::TYPE_RENDER, $data));
        }
        return $this;
    }

    /**
     *
     * Render move action
     *
     * @param array $actionData
     * @return Varien_Simplexml_Element
     */
    protected function _moveRender($actionData)
    {
        $move = new Varien_Simplexml_Element('<move></move>');
        $move->addAttribute('element', $this->getName());

        if (isset($actionData['after'])) {
            $move->addAttribute('after', $actionData['after']);
        } elseif ($actionData['before']) {
            $move->addAttribute('before', $actionData['before']);
        }

        if (isset($actionData['as'])) {
            $move->addAttribute('as', $actionData['as']);
        }

        $move->addAttribute('destination', $actionData['destination_container']);
        return $move;
    }

    /**
     * Render remove action
     *
     * @return Varien_Simplexml_Element
     */
    protected function _removeRender()
    {
        $remove = new Varien_Simplexml_Element('<remove></remove>');
        $remove->addAttribute('name', $this->getName());
        return $remove;
    }
}
