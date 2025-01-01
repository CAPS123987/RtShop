<?php

declare(strict_types=1);

namespace App\Components\BasketlistControl;

use Nette;
use Nette\Application\UI;

use App\Core\SessionStorage;
use App\Core\Basket;
use App\Core\CurrencyTransform;
use Tracy\Debugger;

class BasketlistControl extends UI\Control
{   
    public function __construct(
        private Basket $basket,
        private Nette\Database\Explorer $database,
    )
	{
	}

    public function render(): void
	{   
        $template = $this->template;

        $eurValue = CurrencyTransform::getCurrencyValue('EMU');

        $productsCache = $this->database->table("products")->select("id, name, cost")->fetchAssoc("id");

        $basket = SessionStorage::getValue(SessionStorage::BASKET);

        $items = [];
        
        foreach($basket as $id => $amount) {
            if(!isset($productsCache[$id])){
                continue;
            }
            $item = $productsCache[$id];
            $item['cost'] = $item['cost'] * $amount;
            $item['euCost'] = round($item['cost'] / $eurValue,2);
            $item['amount'] = $amount;
            $items[$id] = $item;
        }
        $template->items = $items;

        $cost = SessionStorage::getValue(SessionStorage::TOTAL_COST);
        $template->cost = $cost;
        
		$template->render(__DIR__ . '/BasketlistControl.latte');
	}
    
    public function handleAdd($id): void
    {
        $this->basket->addItem(intval($id), 1);
    }

    public function handleRemove($id): void
    {
        $this->basket->removeItem(intval($id), 1);
    }

    public function handleSet($id,$amount): void
    {
        $this->basket->setItem(intval($id), intval($amount));
    }

    public function handleEmpty(): void
    {
        $this->basket->clearBasket();
    }
}