<?php

declare(strict_types=1);

namespace App\Components\BasketControl;

use Nette\Application\UI;

use App\Core\SessionStorage;
use App\Core\Basket;


class BasketControl extends UI\Control
{   
    public function __construct(
        private Basket $basket,
    )
	{
	}

    public function render(): void
	{   
        $template = $this->template;

        $amount = SessionStorage::getValue(SessionStorage::AMOUNT);
        $template->amount = $amount;
        
        $cost = SessionStorage::getValue(SessionStorage::TOTAL_COST);
        $template->cost = $cost;
        
		$template->render(__DIR__ . '/BasketControl.latte');
	}
    
    public function handleBuy(string $id): void
    {
        $this->basket->addItem(intval($id), 1);
        $this->flashMessage('Produkt byl přidán do košíku.', 'success');
    }

    public function handleEmpty(): void
    {
        $this->basket->clearBasket();
    }
}