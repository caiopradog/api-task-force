<?php
function priceFormat($price)
{
	$valueFormatado = str_replace('.', '', $price);
	$valueFormatado = str_replace(',', '.', $valueFormatado);

	return $valueFormatado;
}

function dateConverter($dateString) {
    return date('Y-m-d', strtotime(str_replace('/', '-', $dateString)));
}