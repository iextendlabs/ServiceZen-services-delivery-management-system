<?php

use App\Models\StaffZone;
use Illuminate\Support\Facades\Request;

function formatCurrency($amount, $isAmin ,$extra_charges = false)
{
    if($isAmin == true){
        $decimalSeparator = '.';
        $thousandsSeparator = ',';
        $symbol = 'AED';
        $formattedAmount = $symbol . number_format((float)$amount, 2, $decimalSeparator, $thousandsSeparator);

        return $formattedAmount;
    }else{
        try {
            $address = Request::cookie('address');
            $address = json_decode($address, true);
            $zoneName = $address['area'] ?? null;
    
            $decimalSeparator = '.';
            $thousandsSeparator = ',';
    
            $symbol = 'AED';
            $modifiedAmount = $amount;
    
            if ($zoneName) {
                $staffZone = StaffZone::where('name', $zoneName)->first();
    
                if ($staffZone) {
                    if (!isset($staffZone->currency) && isset($staffZone->extra_charges)) {
                        $extraCharges = $extra_charges === true ? floatval($staffZone->extra_charges) : 0;
                        $modifiedAmount = $amount + $extraCharges;
                    } elseif (isset($staffZone->currency)) {
                        $symbol = $staffZone->currency->symbol;
                        $currencyRate = floatval($staffZone->currency->rate);
                        $extraCharges = $extra_charges == true ? floatval($staffZone->extra_charges) : 0;
                        $modifiedAmount = ($amount + $extraCharges) * $currencyRate;
                    }
                }
            }
    
            $formattedAmount = $symbol . number_format((float)$modifiedAmount, 2, $decimalSeparator, $thousandsSeparator);
    
            return $formattedAmount;
        } catch (\Throwable $th) {
            $decimalSeparator = '.';
            $thousandsSeparator = ',';
            $symbol = 'AED';
            $formattedAmount = $symbol . number_format((float)$amount, 2, $decimalSeparator, $thousandsSeparator);
    
            return $formattedAmount;
        }
    }
}
