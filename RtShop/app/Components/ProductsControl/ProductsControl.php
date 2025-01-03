<?php

declare(strict_types=1);

namespace App\Components\ProductsControl;

use Nette;
use Nette\Application\UI;

use App\Core\SessionStorage;
use App\Core\Basket;
use App\Core\CurrencyTransform;
use Tracy\Debugger;
use Nette\Utils\Json;

class ProductsControl extends UI\Control
{   
    public const pageSize = 21;

    public function __construct(
        private Nette\Database\Explorer $database,
    )
	{
	}

    public function render(): void
	{   
        $template = $this->template;

        if(!isset($this->template->isSet)) {
            $this->renderItems("","[]",0);
            $this->template->maxPage = ceil($this->database->table('products')->count() / ProductsControl::pageSize);
        }

        
		$template->render(__DIR__ . '/ProductsControl.latte');
	}
    
    public function handleSearch($query,$tagsRaw, $offset): void
    {
        $this->renderItems($query,$tagsRaw,intval($offset));
        $this->template->isSet = true;
    }

    public function renderItems($query, $tagsRaw, $offset): void
    {
        $offset = $offset * ProductsControl::pageSize;

        $eurValue = CurrencyTransform::getCurrencyValue("EMU");

        $tagCache = $this->database->table('tags')->select('id, name')->fetchAssoc('id');
        $tags = JSON::decode($tagsRaw, forceArrays: true);
        Debugger::barDump($tags);

        $queryMask = "%$query%";
        $products = $this->database->table('products')->select('id, name, cost, tags')->where('name LIKE ?',$queryMask)->limit(ProductsControl::pageSize,$offset)->fetchAssoc('id');
        
        foreach($products as $product) {

            $tagIds = JSON::decode($product['tags'], forceArrays: true);
            $tagNames = [];

            if(!$this->validateTags($tags,$tagIds)) {
                unset($products[$product['id']]);
                continue;
            }

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

        $this->template->maxPage = ceil(count($products) / ProductsControl::pageSize);
        $this->template->items = $products;
    }

    private function validateTags($requiredTags,$tags): bool
    {
        foreach($requiredTags as $tag) {
            if(!in_array($tag,$tags)) {
                return false;
            }
        }
        return true;
    }
}