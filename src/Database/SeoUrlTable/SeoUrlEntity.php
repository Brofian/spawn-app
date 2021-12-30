<?php

namespace spawnApp\Database\SeoUrlTable;


class SeoUrlEntity extends SeoUrlEntityDefinition
{



    public function getLabel(): string {
        return $this->getController() . ' | ' . $this->getAction();
    }


}