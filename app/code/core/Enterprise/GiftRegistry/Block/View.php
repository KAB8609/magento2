<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry view block
 */
class Enterprise_GiftRegistry_Block_View extends Enterprise_GiftRegistry_Block_Customer_Items
{
    /**
     * Return current giftregistry entity
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('current_entity');
    }

    /**
     * Retrieve entity formated date
     *
     * @param string $date
     * @return string
     */
    public function getFormattedDate($date)
    {
        if ($date) {
            return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        }
        return '';
    }

    /**
     * Retrieve entity country name
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        if ($countryCode) {
            $country = Mage::getModel('Mage_Directory_Model_Country')->loadByCode($countryCode);
            return $country->getName();
        }
        return '';
    }

    /**
     * Retrieve comma-separated list of entity registrant roles
     *
     * @param string $attributeCode
     * @param Enterprise_GiftRegistry_Model_Type $type
     * @return string
     */
    public function getRegistrantRoles($attributeCode, $type)
    {
        if ($registrantRoles = $this->getEntity()->getRegistrantRoles()) {
            $roles = array();
            foreach ($registrantRoles as $code) {
                if ($label = $type->getOptionLabel($attributeCode, $code)) {
                    $roles[] = $label;
                }
            }
            if (count($roles)) {
                return implode(', ', $roles);
            }
        }
        return '';
    }

    /**
     * Retrieve attributes to display info array
     *
     * @return array
     */
    public function getAttributesToDisplay()
    {
        $typeId = $this->getEntity()->getTypeId();
        $type = Mage::getModel('Enterprise_GiftRegistry_Model_Type')->load($typeId);

        $attributes = array_merge(
            array(
                'title' => $this->__('Event'),
                'registrants' => $this->__('Recipient')
            ),
            $type->getListedAttributes(),
            array(
                'customer_name' => $this->__('Registry owner'),
                'message' => $this->__('Message')
            )
        );

        $result = array();
        foreach ($attributes as $attributeCode => $attributeTitle) {
            switch($attributeCode) {
                case 'customer_name' :
                    $attributeValue = $this->getEntity()->getCustomer()->getName();
                    break;
                case 'event_date' :
                    $attributeValue = $this->getFormattedDate($this->getEntity()->getEventDate());
                    break;
                 case 'event_country' :
                    $attributeValue = $this->getCountryName($this->getEntity()->getEventCountry());
                    break;
                 case 'role' :
                    $attributeValue = $this->getRegistrantRoles($attributeCode, $type);
                    break;
                default :
                    $attributeValue = $this->getEntity()->getDataUsingMethod($attributeCode);
            }

            if ((string)$attributeValue == '') {
                continue;
            }
            $result[] = array(
                'title' => $attributeTitle,
                'value' => $this->escapeHtml($attributeValue)
            );
        }
        return $result;
    }
}
