<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\TableDefinition\ColumnDefinition;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\SimpleResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Response;
use spawnApp\Database\MigrationTable\MigrationRepository;

class AdminLoginController extends AbstractBackendController {

    public static function getSidebarMethods(): array
    {
        return [
            'configuration' => [
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'administratorOverviewAction',
                        'title' => 'Administrator'
                    ],
                ]
            ]
        ];
    }

    /**
     * @route /backend/login
     * @locked
     * @return AbstractResponse
     */
    public function loginAction(): AbstractResponse {

        return new SimpleResponse('TODO: Login window');
    }

    /**
     * @route /backend/admin/overview
     * @locked
     * @return AbstractResponse
     */
    public function administratorOverviewAction(): AbstractResponse {

        return new SimpleResponse('TODO: Administrator overview');
    }
}