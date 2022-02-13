<?php

namespace SpawnCore\Defaults\Database\ModuleTable;



class ModuleEntity extends ModuleEntityDefinition
{

    public static function sortModuleEntityArrayByWeight(array $moduleEntities): array
    {
        usort($moduleEntities, static function ($a, $b) {
            /** @var $a ModuleEntity */
            /** @var $b ModuleEntity */
            $aWeight = $a->getResourceConfigValue('weight', 0);
            $bWeight = $b->getResourceConfigValue('weight', 0);

            return $aWeight <=> $bWeight;
        });

        return $moduleEntities;
    }

}