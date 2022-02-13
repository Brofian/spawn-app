<?php

namespace SpawnCore\Defaults\Database\ModuleTable;



class ModuleEntity extends ModuleEntityDefinition
{

    public static function sortModuleEntityArrayByWeight(array $moduleEntities): array
    {
        usort($moduleEntities, function ($a, $b) {
            /** @var $a ModuleEntity */
            /** @var $b ModuleEntity */
            $aWeight = $a->getResourceConfigValue('weight', 0);
            $bWeight = $b->getResourceConfigValue('weight', 0);

            if ($aWeight < $bWeight) return -1;
            else if ($aWeight > $bWeight) return 1;
            else return 0;
        });

        return $moduleEntities;
    }

}