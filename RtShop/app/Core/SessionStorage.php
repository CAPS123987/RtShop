<?php

namespace App\Core;

use Nette;
use Tracy\Debugger;

final class SessionStorage {
    public const sesionValues = ["totalCost"=>0,"basket"=>[],"amount"=>0];

    public const TOTAL_COST = "totalCost";
    public const BASKET = "basket";
    public const AMOUNT = "amount";

    public static function initAllValues() : void
    { 
        foreach(SessionStorage::sesionValues as $valueName => $defaultValue) {
            if(!isset($_SESSION[$valueName])) {
                $_SESSION[$valueName] = $defaultValue;
            }
        }
    }

    public static function getValue($valueName)
    {
        SessionStorage::validate($valueName);
        return $_SESSION[$valueName];
    }

    public static function setValue($valueName, $value) : void
    {
        SessionStorage::validate($valueName);
        $_SESSION[$valueName] = $value;
    }

    public static function validate($valueName) : void
    {
        if(!isset($_SESSION[$valueName])) {
            $_SESSION[$valueName] = SessionStorage::getDefaultValue($valueName);
        }
    }

    public static function getDefaultValue($valueName)
    {
        return SessionStorage::sesionValues[$valueName];
    }
}