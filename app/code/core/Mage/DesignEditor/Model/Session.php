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
 * Design editor session model
 */
class Mage_DesignEditor_Model_Session extends Mage_Admin_Model_Session
{
    /**
     * Session key that indicates whether the design editor is active
     */
    const SESSION_DESIGN_EDITOR_ACTIVE = 'DESIGN_EDITOR_ACTIVE';

    /**
     * Check whether the design editor is active for the current session or not
     *
     * @return bool
     */
    public function isDesignEditorActive()
    {
        if ($this->getData(self::SESSION_DESIGN_EDITOR_ACTIVE)) {
            if ($this->isLoggedIn()) {
                return true;
            }
            /* Admin session has been expired */
            $this->deactivateDesignEditor();
        }
        return false;
    }

    /**
     * Activate the design editor for the current session
     */
    public function activateDesignEditor()
    {
        $this->setData(self::SESSION_DESIGN_EDITOR_ACTIVE, 1);
        Mage::dispatchEvent('design_editor_session_activate');
    }

    /**
     * Deactivate the design editor for the current session
     */
    public function deactivateDesignEditor()
    {
        $this->unsetData(self::SESSION_DESIGN_EDITOR_ACTIVE);
        Mage::dispatchEvent('design_editor_session_deactivate');
    }

    /**
     * Sets skin to user session, so that next time everything will be rendered with this skin
     *
     * @param string $skin
     * @return Mage_DesignEditor_Model_Session
     */
    public function setSkin($skin)
    {
        if ($skin && !$this->_isSkinApplicable($skin)) {
            Mage::throwException(Mage::helper('Mage_DesignEditor_Helper_Data')->__("Skin doesn't exist"));
        }
        $this->setData('skin', $skin);
        return $this;
    }

    /**
     * Returns whether a skin is a valid one to set into user session
     *
     * @param string $skin
     * @return bool
     */
    protected function _isSkinApplicable($skin)
    {
        if (!$skin) {
            return false;
        }
        $options = Mage::getModel('Mage_Core_Model_Design_Source_Design')->getOptions();
        foreach ($options as $optGroup) {
            foreach ($optGroup['value'] as $option) {
                if ($option['value'] == $skin) {
                    return true;
                }
            }
        }
        return false;
    }
}
