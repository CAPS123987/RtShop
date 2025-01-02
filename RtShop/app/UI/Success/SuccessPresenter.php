<?php

declare(strict_types=1);

namespace App\UI\Success;

use Nette;



final class SuccessPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    public function renderDefault(): void
    {
        
    }
}
