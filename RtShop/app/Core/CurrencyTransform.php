<?php

declare(strict_types=1);

namespace App\Core;

final class CurrencyTransform 
{
    public static function getCurrencyValue(String $currencyName) : float
    {   
        $content = file_get_contents('https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt');

        $lines = explode("\n", $content);

        foreach($lines as $line) {
            if(!str_starts_with($line,"EMU")) {
                continue;
            }

            return CurrencyTransform::getLastParam($line);
        }
        return -1;
    }

    
    private static function getLastParam($line) : float
    {
        $builder = '';
        for($i = strlen($line)-1;$line[$i]!='|';$i--) {
            if($line[$i] == ',') {
                $line[$i] = '.';
            }
            $builder = $line[$i] . $builder;
        }

        return floatval($builder);
    }
}