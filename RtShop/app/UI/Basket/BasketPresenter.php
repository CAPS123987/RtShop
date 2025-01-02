<?php

declare(strict_types=1);

namespace App\UI\Basket;

use Nette;
use Tracy\Debugger;
use Nette\Utils\Json;
use App\Core\CurrencyTransform;
use App\Core\SessionStorage;
use App\Core\Basket;
use App\Components\BasketControl\BasketControl;
use App\Components\BasketlistControl\BasketlistControl;

use Nette\Application\UI\Form;



final class BasketPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Nette\Database\Explorer $database,
        private Basket $basket,
    ) {
    }

    public function renderDefault(): void
    {
        if(session_status()!=PHP_SESSION_ACTIVE) {
            session_start();
        }
        SessionStorage::initAllValues();
    }

    protected function createComponentBasketlist(): BasketlistControl
    {
        $basket = new BasketlistControl($this->basket,$this->database);
        $basket->redrawControl();
        return $basket;
    }

    protected function createComponentOrderform(): Form
    {
        $form = new Form;
        $form->addText("name","Jméno: ")->setRequired();
        $form->addEmail("email","Email: ")->setRequired();
        $form->addText("adress","Adresa: ")->setRequired();
        $form->addSubmit("send","odeslat objednávku");

        $form->onSuccess[] = $this->postFormSucceeded(...);
        return $form;
    }

    private function postFormSucceeded(array $data)
    {
        $data["items"] = JSON::encode($this->basket->getBasket());
        $this->database->table("orders")->insert($data);
        $this->basket->clearBasket();

        $this->flashMessage("Objednávka byla odeslána");
        $this->redirect("Success:default");
    }
}
