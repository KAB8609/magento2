<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating Tax Rate
 *
 * @package Magento\Tax\Test\Handler\Curl
 */
class CreateTaxRate extends Curl
{
    /**
     * Post request for creating tax rate
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        $url = $_ENV['app_backend_url'] . 'tax/rate/ajaxSave/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();
        return $this->_getTaxRateId($response);
    }

    /**
     * Return saved rate id
     *
     * @param string $data
     * @return int|null
     */
    protected function _getTaxRateId($data)
    {
        $data = json_decode($data);
        return isset($data->tax_calculation_rate_id) ? (int)$data->tax_calculation_rate_id : null;
    }
}