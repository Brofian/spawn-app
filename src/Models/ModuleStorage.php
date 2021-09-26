<?php


namespace spawnApp\Models;


use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use spawn\system\Core\Base\Database\DatabaseConnection;
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
        $conn = $connection::getConnection();

        try {
            $tableName = ModuleTable::TABLE_NAME;
            $selStmt = $conn->prepare("SELECT id AS count FROM $tableName WHERE slug = ?");
            $selStmt->bindValue(1, $this->slug);
            /** @var Result $result */
            $result = $selStmt->executeQuery();

            if($result->rowCount() > 0) {
                $this->id = UUID::bytesToHex($result->fetchOne());
            }
        }catch (Exception $e) {
        } catch (\Doctrine\DBAL\Exception $e) {
        }

        $currentTimestamp = new \DateTime();

        if($this->id === null) {
            $randomBytes = UUID::randomBytes();
            $this->setId(UUID::bytesToHex($randomBytes));

            $conn->insert(ModuleTable::TABLE_NAME,
                [
                    'slug' => $this->slug,
                    'path' => $this->path,
                    'active' => $this->active,
                    'information' => $this->informations,
                    'resourceConfig' => $this->resourceConfig,
                    'id' => $randomBytes,
                    'createdAt' => $currentTimestamp,
                    'updatedAt' => $currentTimestamp
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_BOOL,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    'datetime',
                    'datetime'
                ]);
        }
        else {
            $conn->update(ModuleTable::TABLE_NAME,
                [
                    'slug' => $this->slug,
                    'path' => $this->path,
                    'active' => $this->active,
                    'information' => $this->informations,
                    'resourceConfig' => $this->resourceConfig,
                    'updatedAt' => $currentTimestamp
                ],
                [
                    'id' => UUID::hexToBytes($this->id)
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_BOOL,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    'datetime',
                    \PDO::PARAM_STR,
                ]);
        }
    }


    /**
     * @param DatabaseConnection $connection
     * @param bool $onlyActive
     * @return ModuleStorage[]
     * @throws \Doctrine\DBAL\Exception
     */
    public static function findAll(DatabaseConnection $connection, bool $onlyActive = false) {
        $qb = $connection::getConnection()->createQueryBuilder();

        $select = $qb->select("*")->from(ModuleTable::TABLE_NAME);
        if($onlyActive) {
            $select->where('active', 1);
        }
        $erg = $select->executeQuery();


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




    public function delete(DatabaseConnection $connection) {
        if($this->id === null) return;

        $qb = new QueryBuilder($connection::getConnection());
        $qb->delete()
            ->from(ModuleTable::TABLE_NAME)
            ->where('id', $this->id)
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