<?php

function formatCurrency($amount)
{
    $currencyCode = config('app.currency');
    // Define currency symbols and decimal separators
    $currencySymbols =config('currencies');

    $decimalSeparator = '.';
    $thousandsSeparator = ',';

    // Retrieve currency symbol for the given currency code
    $symbol = $currencySymbols[$currencyCode] ?? '';

    // Format the amount with proper separators and currency symbol
    $formattedAmount = $symbol . number_format((float)$amount, 2, $decimalSeparator, $thousandsSeparator);

    return $formattedAmount;
}
