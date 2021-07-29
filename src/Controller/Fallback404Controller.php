<?php

namespace spawnApp\Controller;


use spawn\system\Core\Base\Controller\AbstractController;
use spawn\system\Core\Helper\FrameworkHelper\DatabaseStructureHelper;


class Fallback404Controller extends AbstractController  {

    public function error404Action() {

        $dbStructureHelper = new DatabaseStructureHelper();
        $dbStructureHelper->createDatabaseStructure();

        dd("404 default");
    }

}