<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SeoUrlTable;


class SeoUrlEntity extends SeoUrlEntityDefinition
{



    public function getLabel(): string {
        return $this->getController() . ' | ' . $this->getAction();
    }


}