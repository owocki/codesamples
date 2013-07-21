<?

/**
 * This file will be used to store helper functions for this app.
 */

/**
 * [reindex description]
 * @param  array $arr an array
 * @return array   the input array, but with the indices reset to 0 and sequentially onwards.
 */
function reindex(&$arr) { 
	$tmp = $arr; 
	$arr = array(); 
	foreach($tmp as $value) { 
		$arr[] = $value;  
	}  
}  

?>