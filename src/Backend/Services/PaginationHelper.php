<?php

namespace SpawnBackend\Services;

use SpawnCore\System\CardinalSystem\Request;

class PaginationHelper {

    protected Request $request;

    public function __construct(
        Request $request
    )
    {
        $this->request = $request;
    }



    public function getPaginationTwigData(): array {
        return [];
    }


}