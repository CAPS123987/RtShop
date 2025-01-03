<?php

declare(strict_types=1);

namespace App\UI\Home;

use Nette;
use Tracy\Debugger;
use Nette\Utils\Json;
use App\Core\CurrencyTransform;
use App\Core\SessionStorage;
use App\Core\Basket;
use App\Components\BasketControl\BasketControl;
use App\Components\ProductsControl\ProductsControl;
use Nette\Application\UI\Form;



final class HomePresenter extends Nette\Application\UI\Presenter
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

        //$this->renderItems();
        $paramArray = $this->request->getParameters();
        if(isset($paramArray['products-query'])) {
            $this->template->query = $paramArray['products-query'];
        }

        if(isset($paramArray['products-tagsRaw'])) {
            $this->template->selectedTags = JSON::decode($paramArray['products-tagsRaw'], forceArrays: true);
        }

        $tags = $this->database->table('tags')->select('id, name')->fetchAssoc('id');
        
        $this->template->tags = $tags;
    }

    protected function createComponentBasket(): BasketControl
    {
        $basket = new BasketControl($this->basket);
        $basket->redrawControl();
        return $basket;
    }

    protected function createComponentProducts(): ProductsControl
    {
        $product = new ProductsControl($this->database);
        $product->redrawControl();
        return $product;
    }

    public function renderItems() : void
    {
        $eurValue = CurrencyTransform::getCurrencyValue("EMU");

        $tagCache = $this->database->table('tags')->select('id, name')->fetchAssoc('id');

        $products = $this->database->table('products')->select('id, name, cost, tags')->fetchAssoc('id');
        
        foreach($products as $product) {

            $tagIds = JSON::decode($product['tags'], forceArrays: true);
            $tagNames = [];

            foreach($tagIds as $tagId) {
                if (isset($tagCache[$tagId])) {
                    $tagNames[$tagId] = $tagCache[$tagId]['name'];
                } else {
                    $tagNames[$tagId] = "Not found";
                }
            }
            
            $products[$product['id']]['tags'] = $tagNames;
            
            $products[$product['id']]['euCost'] = round($product['cost'] / $eurValue,2);
        }

        $this->template->items = $products;
    }
}
