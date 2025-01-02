<?php

namespace App\Core;

use Nette;
use Nette\Database\Table\Selection;
use Nette\Http\Session;

final class Basket {

    public function __construct(
        private Nette\Database\Explorer $database,
    )
	{
	}

    public function addItem(int $id, int $amount) : void
    {
        $basket = SessionStorage::getValue(SessionStorage::BASKET);

        if(array_key_exists($id, $basket)) {
            $basket[$id] += $amount;
        } else {
            $basket[$id] = $amount;
        }

        SessionStorage::setValue(SessionStorage::BASKET, $basket);

        $this->calculateOthers();
    }

    public function removeItem(int $id, int $amount) : void
    {
        $basket = SessionStorage::getValue(SessionStorage::BASKET);
        
        if(!array_key_exists($id, $basket)) {
            return;
        }

        if($basket[$id] <= $amount) {
            $amount = $basket[$id];
            unset($basket[$id]);
        } else {
            $basket[$id] -= $amount;
        }

        SessionStorage::setValue(SessionStorage::BASKET, $basket);
        
        $this->calculateOthers();
    }
    
    public function setItem(int $id, int $amount) : void
    {
        $basket = SessionStorage::getValue(SessionStorage::BASKET);

        if(!array_key_exists($id, $basket)) {
            return;
        }
        
        if($amount<=0) {
            unset($basket[$id]);
        }else {
            $basket[$id] = $amount;
        }
        
        SessionStorage::setValue(SessionStorage::BASKET, $basket);

        $this->calculateOthers();
    }

    public function clearBasket() : void
    {
        SessionStorage::setValue(SessionStorage::AMOUNT, SessionStorage::getDefaultValue(SessionStorage::AMOUNT));
        SessionStorage::setValue(SessionStorage::BASKET, SessionStorage::getDefaultValue(SessionStorage::BASKET));
        SessionStorage::setValue(SessionStorage::TOTAL_COST, SessionStorage::getDefaultValue(SessionStorage::TOTAL_COST));
    }

    public function getBasket()
    {
        return SessionStorage::getValue(SessionStorage::BASKET);
    }

    public function getItemCost($id)
    {
        return $this->database->table('products')->where('id', $id)->select('cost')->fetch()->cost;
    }

    public function calculateOthers() : void
    {
        $data = $this->calculateValues($this->getBasket());
        
        SessionStorage::setValue(SessionStorage::AMOUNT,$data["amount"]);
        SessionStorage::setValue(SessionStorage::TOTAL_COST,$data["cost"]);
    }

    public function calculateValues($basket) : array
    {
        $costCache = $this->database->table("products")->select("id, cost")->fetchAssoc("id");

        $amountBuilder = 0;
        $costBuilder = 0;
        foreach ($basket as $id => $amount) {
            $amountBuilder += $amount;
            
            if(!array_key_exists($id, $costCache)) {
                continue;
            }

            $costBuilder += $costCache[$id]["cost"] * $amount;
        }

        return ["amount"=>$amountBuilder, "cost"=>$costBuilder];
    }
}