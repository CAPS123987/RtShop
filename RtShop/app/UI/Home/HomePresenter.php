<?php

declare(strict_types=1);

namespace App\UI\Home;

use Nette;
use Tracy\Debugger;
use Nette\Utils\Json;



final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    public function renderDefault(): void
    {
        $products = $this->database->table('products')->select('id, name, cost, tags')->fetchAssoc('id');
        
        foreach($products as $product) {
            $decode = JSON::decode($product['tags'], forceArrays: true);

            $products[$product['id']]['tags'] = $decode;
        }

        $this->template->items = $products;



        Debugger::barDump($products);
    }
}
