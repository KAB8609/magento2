<?php

require_once 'Varien/File/Csv.php';

class Varien_File_Csv_multy extends Varien_File_Csv {
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
        	    		array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $line_number,'value' => isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null));						
        	    	} else {
        	    		$tmp_value = $data[$rowData[$keyIndex]]['value'];
        	    		$tmp_line  = $data[$rowData[$keyIndex]]['line'];
	        	    	$data[$rowData[$keyIndex]]['duplicate'] = array();
	        	    	array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $tmp_line,'value' => $tmp_value));
						array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $line_number,'value' => isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null));						
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