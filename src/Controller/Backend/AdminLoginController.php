<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\TableDefinition\ColumnDefinition;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Response;
use spawnApp\Database\MigrationTable\MigrationRepository;

class AdminLoginController extends AbstractBackendController {

    public static function getSidebarMethods(): array
    {
        return [];
    }

    /**
     * @route /backend/login
     * @locked
     * @return AbstractResponse
     */
    public function loginAction(): AbstractResponse {


    }
}