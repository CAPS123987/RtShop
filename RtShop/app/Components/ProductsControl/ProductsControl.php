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
    
    public function handleSearch($query,$tagsRaw): void
    {
        $this->renderItems($query,$tagsRaw);
    }

    public function renderItems($query, $tagsRaw): void
    {
        $eurValue = CurrencyTransform::getCurrencyValue("EMU");

        $tagCache = $this->database->table('tags')->select('id, name')->fetchAssoc('id');
        $tags = JSON::decode($tagsRaw, forceArrays: true);
        Debugger::barDump($tags);

        $queryMask = "%$query%";
        $products = $this->database->table('products')->select('id, name, cost, tags')->where('name LIKE ?',$queryMask)->fetchAssoc('id');
        
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