<?php


namespace spawnApp\Models;


use spawn\Database\StructureTables\SpawnModuleActions;
use spawn\Database\StructureTables\SpawnModules;
use spawn\system\Core\Base\Database\DatabaseConnection;
use spawn\system\Core\Base\Database\Query\QueryBuilder;
use spawn\system\Core\Helper\UUID;
use spawnApp\Database\ModuleTable\ModuleTable;

class ModuleStorage {

    protected ?string $id = null;
    protected string $slug = "";
    protected string $path = "";
    protected bool $active = false;
    protected string $informations = "";
    protected string $resourceConfig = "";


    public function __construct(string $slug, string $path, bool $active = false, string $informations = "", string $resourceConfig = "", ?string $id = null)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->path = $path;
        $this->active = $active;
        $this->informations = $informations;
        $this->resourceConfig = $resourceConfig;
    }

    public function save(DatabaseConnection $connection) {
        $qb = new QueryBuilder($connection::getConnection());
        $selStmt = $qb->select('id, count(*) as count')->from(ModuleTable::TABLE_NAME)->where('slug', $this->slug)->execute();

        if($selStmt[0]['count'] > 0) {
            $this->id = $selStmt[0]['id'];
        }

        if($this->id === null) {
            $randomBytes = UUID::randomBytes();
            $this->setId(UUID::bytesToHex($randomBytes));

            $qb->insert()->into(ModuleTable::TABLE_NAME)
                ->setValue('slug', $this->slug)
                ->setValue('path', $this->path)
                ->setValue('active', $this->active)
                ->setValue('information', $this->informations)
                ->setValue('resourceConfig', $this->resourceConfig)
                ->setValue('id', $randomBytes)
                ->execute();
        }
        else {
            $qb->update(ModuleTable::TABLE_NAME)
                ->where('id', UUID::hexToBytes($this->id))
                ->set('slug', $this->slug)
                ->set('path', $this->path)
                ->set('active', $this->active)
                ->set('information', $this->informations)
                ->set('resourceConfig', $this->resourceConfig)
                ->execute();
        }
    }


    /**
     * @param DatabaseConnection $connection
     * @param bool $onlyActive
     * @return ModuleStorage[]
     */
    public static function findAll(DatabaseConnection $connection, bool $onlyActive = false) {
        $qb = new QueryBuilder($connection::getConnection());

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
                    $module['slug'],
                    $module['path'],
                    $module['active'],
                    $module['information'],
                    $module['resourceConfig'],
                    UUID::bytesToHex($module['id'])
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

        $qb = new QueryBuilder($connection::getConnection());
        $qb->delete()
            ->from(SpawnModules::TABLENAME)
            ->where(SpawnModules::RAW_COL_ID, $this->id)
            ->execute();

        $this->id = null;
    }













    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }


    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getInformations(): string
    {
        return $this->informations;
    }

    public function setInformations(string $informations): self
    {
        $this->informations = $informations;
        return $this;
    }

    public function getResourceConfig(): string
    {
        return $this->resourceConfig;
    }

    public function setResourceConfig(string $resourceConfig): self
    {
        $this->resourceConfig = $resourceConfig;
        return $this;
    }



}