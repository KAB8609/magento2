<?php

class Mage_GoogleCheckout_Model_Mysql4_Tax extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_rule', 'rule_id');
    }

    public function fetchRuleRatesForCustomerTaxClass()
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('rule'=>$this->getTable('tax/tax_rule')))
            ->join(array('rd'=>$this->getTable('tax/tax_rate_data')), "rd.rate_type_id=rule.tax_rate_type_id", array('value'=>'rate_value'))
            ->join(array('r'=>$this->getTable('tax/tax_rate')), "r.tax_rate_id=rd.tax_rate_id", array('postcode'=>'tax_postcode'))
            ->joinLeft(array('reg'=>$this->getTable('directory/country_region')), "reg.region_id=r.tax_region_id", array('country'=>'country_id', 'state'=>'code'));
        $rows = $read->fetchAll($select);

        return $rows;
    }
}