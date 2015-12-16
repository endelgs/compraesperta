<?php

function singleItemPrice ($item, $quantity) {
	print_r($item);
	return $item['price'] * $quantity;	
} 

function listPrice ($items) {
	$total = 0;
	foreach($items as $item) {
		$total += singleItemPrice($item['item'], $item['quantity']);
	}

	return $total;
}

?>
