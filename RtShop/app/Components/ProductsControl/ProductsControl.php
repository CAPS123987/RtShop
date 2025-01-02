<?php

declare(strict_types=1);

namespace App\Components\ProductsControl;

use Nette;
use Nette\Application\UI;

use App\Core\SessionStorage;
use App\Core\Basket;
use App\Core\CurrencyTransform;
use Tracy\Debugger;

class ProductsControl extends UI\Control
{   
    public function __construct(
        private Nette\Database\Explorer $database,
    )
	{
	}

    public function render(): void
	{   
        $template = $this->template;
        
		$template->render(__DIR__ . '/ProductsControl.latte');
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