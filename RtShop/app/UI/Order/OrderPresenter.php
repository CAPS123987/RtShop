<?php

declare(strict_types=1);

namespace App\UI\Order;

use App\Core\Basket;
use Nette;
use Tracy\Debugger;
use Nette\Utils\Json;

final class OrderPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    public function renderDefault($orderId): void
    {
        $productCache = $this->database->table("products")->select("id, name, cost")->fetchAssoc("id");

        $orderId = intval($orderId);

        $order = $this->database->table("orders")->select("*")->where("id=$orderId")->fetch();

        $jsonBasket = JSON::decode($order->items,true);

        $basketItems = [];

        foreach($jsonBasket as $id => $amount) {
            if(!array_key_exists($id, $productCache)) {
                continue;
            }

            $basketItems[$id] = [
                "id" => $id,
                "name" => $productCache[$id]["name"],
                "cost" => $productCache[$id]["cost"],
                "amount" => $amount
            ];
        }

        $basketObj = new Basket($this->database);
        $basketData = $basketObj->calculateValues($jsonBasket);

        $this->template->order = $order;
        $this->template->basketItems = $basketItems;
        $this->template->amount = $basketData["amount"];
        $this->template->cost = $basketData["cost"];
    }

    public function handleDone($id) : void 
    {
        $this->database->table("orders")->where("id=$id")->update(["status" => "done"]);
        $this->redirect("Orders:default");
    }
}