<?php

declare(strict_types=1);

namespace App\UI\Orders;

use Nette;

final class OrdersPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    public function renderDefault(): void
    {
        $orders = $this->database->table("orders")->select("id, name, date, status")->where('status="pending"')->fetchAssoc("id");

        foreach($orders as $id => $order) {
            $orders[$id]["date"] = $order["date"]->format("d.m.Y H")." hod";
        }

        $this->template->orders = $orders;
    }
}