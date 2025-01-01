<?php

declare(strict_types=1);

namespace App\Components;

use Nette\Application\UI;

use App\Core\SessionStorage;


class BasketControl extends UI\Control
{   
    public function __construct()
	{
	}

    public function render(): void
	{   
        $template = $this->template;

        $amount = SessionStorage::getValue(SessionStorage::AMOUNT);
        $template->amount = $amount;
        
		$template->render(__DIR__ . '/BasketControl.latte');
	}
    
    public function handleBuy(string $id): void
    {
        $amount = SessionStorage::getValue(SessionStorage::AMOUNT);
        SessionStorage::setValue(SessionStorage::AMOUNT, $amount + 1);
    }

    public function handleEmpty(): void
    {
        SessionStorage::setValue(SessionStorage::AMOUNT, 0);
    }
}