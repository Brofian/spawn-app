<?php


namespace spawnApp\Models;


use spawn\Database\StructureTables\SpawnModuleActions;
use spawn\Database\StructureTables\SpawnModules;
use spawn\system\Core\Base\Database\DatabaseConnection;
use spawn\system\Core\Base\Database\Query\QueryBuilder;
use spawn\system\Core\Base\Database\Query\QueryCondition;
use spawn\system\Core\Base\Database\Storage\DatabaseDefaults;
use spawn\system\Core\Base\Database\Storage\DatabaseType;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\Contents\Modules\Module;

class ModuleStorage {

    /** @var int */
    protected $id = null;
    /** @var string */
    protected $slug = "";
    /** @var string */
    protected $path = "";
    /** @var bool */
    protected $active = false;
    /** @var string */
    protected $informations = "";
    /** @var string */
    protected $resourceConfig = "";


    public function __construct(string $slug, string $path, bool $active = false, string $informations = "", string $resourceConfig = "", int $id = null)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->path = $path;
        $this->active = $active;
        $this->informations = $informations;
        $this->resourceConfig = $resourceConfig;
    }

    public function save(DatabaseConnection $connection) {
        $qb = new QueryBuilder($connection);
        if($this->id === null) {
            $qb->insert()->into(SpawnModules::TABLENAME)
                ->setValue(SpawnModules::RAW_COL_SLUG, $this->slug)
                ->setValue(SpawnModules::RAW_COL_PATH, $this->path)
                ->setValue(SpawnModules::RAW_COL_ACTIVE, $this->active)
                ->setValue(SpawnModules::RAW_COL_INFORMATIONS, $this->informations)
                ->setValue(SpawnModules::RAW_COL_RESSOURCE_CONFIG, $this->resourceConfig)
                ->execute();
            $newId = $qb->select(SpawnModules::RAW_COL_ID)
                ->from(SpawnModules::TABLENAME)
                ->orderby(SpawnModules::RAW_COL_ID, true)
                ->limit(1)
                ->execute();
            $this->id = $newId[0][SpawnModules::RAW_COL_ID];
        }
        else {
            $qb->update(SpawnModules::TABLENAME)
                ->where(SpawnModules::RAW_COL_ID, $this->id)
                ->set(SpawnModules::RAW_COL_SLUG, $this->slug)
                ->set(SpawnModules::RAW_COL_PATH, $this->path)
                ->set(SpawnModules::RAW_COL_ACTIVE, $this->active)
                ->set(SpawnModules::RAW_COL_INFORMATIONS, $this->informations)
                ->set(SpawnModules::RAW_COL_RESSOURCE_CONFIG, $this->resourceConfig)
                ->execute();
        }
    }


    public static function findAll(DatabaseConnection $connection, bool $onlyActive = false) {
        $qb = new QueryBuilder($connection);

        $select = $qb->select("*")->from(SpawnModules::TABLENAME);
        if($onlyActive) {
            $select->where(SpawnModules::RAW_COL_ACTIVE, 1);
        }
        $erg = $select->execute();


        if(!$erg) {
            return [];
        }
        else {

            $modules = [];
            foreach ($erg as $module) {
                $modules[] = new self(
                    $module[SpawnModules::RAW_COL_SLUG],
                    $module[SpawnModules::RAW_COL_PATH],
                    $module[SpawnModules::RAW_COL_ACTIVE],
                    $module[SpawnModules::RAW_COL_INFORMATIONS],
                    $module[SpawnModules::RAW_COL_RESSOURCE_CONFIG],
                    $module[SpawnModules::RAW_COL_ID]
                );
            }
            return $modules;
        }
    }


    /**
     * @param DatabaseConnection $connection
     * @return array
     */
    public static function loadAllWithReferences(DatabaseConnection $connection) {

        $qb = new QueryBuilder($connection);
        $unfetchedActions = $qb->select("*")
            ->from(SpawnModules::TABLENAME)
            ->join(
                SpawnModuleActions::TABLENAME,
                SpawnModules::COL_ID,
                SpawnModuleActions::COL_MODULE_ID,
                1)
            ->execute(true);

        return $unfetchedActions;
    }


    public function delete(DatabaseConnection $connection) {
        if($this->id === null) return;

        $qb = new QueryBuilder($connection);
        $qb->delete()
            ->from(SpawnModules::TABLENAME)
            ->where(SpawnModules::RAW_COL_ID, $this->id)
            ->execute();

        $this->id = null;
    }














    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getInformations(): string
    {
        return $this->informations;
    }

    /**
     * @param string $informations
     */
    public function setInformations(string $informations): void
    {
        $this->informations = $informations;
    }

    /**
     * @return string
     */
    public function getResourceConfig(): string
    {
        return $this->resourceConfig;
    }

    /**
     * @param string $resourceConfig
     */
    public function setResourceConfig(string $resourceConfig): void
    {
        $this->resourceConfig = $resourceConfig;
    }



}