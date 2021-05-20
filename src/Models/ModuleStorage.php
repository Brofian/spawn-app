<?php


namespace webuApp\Models;


use webu\Database\StructureTables\WebuModules;
use webu\system\Core\Base\Database\DatabaseConnection;
use webu\system\Core\Base\Database\Query\QueryBuilder;
use webu\system\Core\Base\Database\Query\QueryCondition;
use webu\system\Core\Contents\Modules\Module;

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
            $qb->insert()->into(WebuModules::TABLENAME)
                ->setValue(WebuModules::RAW_COL_SLUG, $this->slug)
                ->setValue(WebuModules::RAW_COL_PATH, $this->path)
                ->setValue(WebuModules::RAW_COL_ACTIVE, $this->active)
                ->setValue(WebuModules::RAW_COL_INFORMATIONS, $this->informations)
                ->setValue(WebuModules::RAW_COL_RESSOURCE_CONFIG, $this->resourceConfig)
                ->execute();
            $newId = $qb->select(WebuModules::RAW_COL_ID)
                ->from(WebuModules::TABLENAME)
                ->orderby(WebuModules::RAW_COL_ID, true)
                ->limit(1)
                ->execute();
            $this->id = $newId[0][WebuModules::RAW_COL_ID];
        }
        else {
            $qb->update(WebuModules::TABLENAME)
                ->where(WebuModules::RAW_COL_ID, $this->id)
                ->set(WebuModules::RAW_COL_SLUG, $this->slug)
                ->set(WebuModules::RAW_COL_PATH, $this->path)
                ->set(WebuModules::RAW_COL_ACTIVE, $this->active)
                ->set(WebuModules::RAW_COL_INFORMATIONS, $this->informations)
                ->set(WebuModules::RAW_COL_RESSOURCE_CONFIG, $this->resourceConfig)
                ->execute();
        }
    }


    public static function findAll(DatabaseConnection $connection, bool $onlyActive = false) {
        $qb = new QueryBuilder($connection);

        $select = $qb->select("*")->from(WebuModules::TABLENAME);
        if($onlyActive) {
            $select->where(WebuModules::RAW_COL_ACTIVE, 1);
        }
        $erg = $select->execute();


        if(!$erg) {
            return [];
        }
        else {

            $modules = [];
            foreach ($erg as $module) {
                $modules[] = new self(
                    $module[WebuModules::RAW_COL_SLUG],
                    $module[WebuModules::RAW_COL_PATH],
                    $module[WebuModules::RAW_COL_ACTIVE],
                    $module[WebuModules::RAW_COL_INFORMATIONS],
                    $module[WebuModules::RAW_COL_RESSOURCE_CONFIG],
                    $module[WebuModules::RAW_COL_ID]
                );
            }
            return $modules;
        }
    }



    public function delete(DatabaseConnection $connection) {
        if($this->id === null) return;

        $qb = new QueryBuilder($connection);
        $qb->delete()
            ->from(WebuModules::TABLENAME)
            ->where(WebuModules::RAW_COL_ID, $this->id)
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