<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_File
 * @copyright  {copyright}
 * @license    {license_link}
 */
 
/**
 * Csv parse
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Magento/File/Csv.php';

class Magento_File_CsvMulty extends Magento_File_Csv {
	/**
     * Retrieve CSV file data as pairs with duplicates
     *
     * @param   string $file
     * @param   int $keyIndex
     * @param   int $valueIndex
     * @return  array
     */
	public function getDataPairs($file, $keyIndex=0, $valueIndex=1)
    {
        $data = array();
        $csvData = $this->getData($file);
        $line_number = 0;
        foreach ($csvData as $rowData) {
        	$line_number++;
        	if (isset($rowData[$keyIndex])) {
        	    if(isset($data[$rowData[$keyIndex]])){
        	    	if(isset($data[$rowData[$keyIndex]]['duplicate'])){
        	    		#array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $line_number,'value' => isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null));						
        	    		$data[$rowData[$keyIndex]]['duplicate']['line'] .=', '.$line_number; 
        	    	} else {
        	    		$tmp_value = $data[$rowData[$keyIndex]]['value'];
        	    		$tmp_line  = $data[$rowData[$keyIndex]]['line'];
	        	    	$data[$rowData[$keyIndex]]['duplicate'] = array();
	        	    	#array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $tmp_line.' ,'.$line_number,'value' => $tmp_value));
	        	    	$data[$rowData[$keyIndex]]['duplicate']['line'] = $tmp_line.' ,'.$line_number;
	        	    	$data[$rowData[$keyIndex]]['duplicate']['value'] = $tmp_value;
        	    	}
        	    } else {
        	    	$data[$rowData[$keyIndex]] = array();
        	    	$data[$rowData[$keyIndex]]['line'] = $line_number;
        			$data[$rowData[$keyIndex]]['value'] = isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null;
        	    }
        	}
        }
        return $data;
    }
	
}
?>