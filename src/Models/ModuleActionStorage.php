<?php


namespace webuApp\Models;


use webu\Database\StructureTables\WebuModuleActions;
use webu\Database\StructureTables\WebuModules;
use webu\system\Core\Base\Database\DatabaseConnection;
use webu\system\Core\Base\Database\Query\QueryBuilder;

class ModuleActionStorage {

    /** @var int  */
    protected $id = null;
    /** @var string  */
    protected $class = "";
    /** @var string  */
    protected $action = "";
    /** @var string */
    protected $custom_url = "";
    /** @var int  */
    protected $module_id = null;
    /** @var string  */
    protected $identifier = "";

    public function __construct(string $class, string $action, string $custom_url, string $identifier, int $moduleId = null, $id = null)
    {
        $this->class = $class;
        $this->action = $action;
        $this->custom_url = $custom_url;
        $this->module_id = $moduleId;
        $this->identifier = $identifier;
        $this->id = $id;
    }


    public function save(DatabaseConnection $connection) {
        if($this->module_id === null) return 3;

        $qb = new QueryBuilder($connection);
        if($this->id === null) {
            $qb->insert()->into(WebuModuleActions::TABLENAME)
                ->setValue(WebuModuleActions::RAW_COL_ACTION, $this->action)
                ->setValue(WebuModuleActions::RAW_COL_CLASS, $this->class)
                ->setValue(WebuModuleActions::RAW_COL_CUSTOM_URL, $this->custom_url)
                ->setValue(WebuModuleActions::RAW_COL_IDENTIFIER, $this->identifier)
                ->setValue(WebuModuleActions::RAW_COL_MODULE_ID, $this->module_id)
                ->execute();
            $newId = $qb->select("SELECT id FROM ".WebuModuleActions::TABLENAME." order by id desc limit 1");
            $this->id = $newId;
        }
        else {
            $qb->update(WebuModuleActions::TABLENAME)
                ->where(WebuModuleActions::RAW_COL_ID, $this->id)
                ->set(WebuModuleActions::RAW_COL_ACTION, $this->action)
                ->set(WebuModuleActions::RAW_COL_CLASS, $this->class)
                ->set(WebuModuleActions::RAW_COL_CUSTOM_URL, $this->custom_url)
                ->set(WebuModuleActions::RAW_COL_IDENTIFIER, $this->identifier)
                ->set(WebuModuleActions::RAW_COL_MODULE_ID, $this->module_id)
                ->execute();
        }
    }

    public function delete(DatabaseConnection $connection) {
        if($this->id === null) return;

        $qb = new QueryBuilder($connection);
        $qb->delete()
            ->from(WebuModuleActions::TABLENAME)
            ->where(WebuModuleActions::RAW_COL_ID, $this->id)
            ->execute();

        $this->id = null;
    }



    public static function findAll(DatabaseConnection $connection, int $moduleId = null) {
        $qb = new QueryBuilder($connection);

        $select = $qb->select("*")->from(WebuModuleActions::TABLENAME);
        if($moduleId !== null) {
            $select->where(WebuModuleActions::RAW_COL_MODULE_ID, $moduleId);
        }

        $erg = $select->execute();


        if(!$erg) {
            return [];
        }
        else {

            $moduleActions = [];
            foreach ($erg as $action) {
                $moduleActions[] = new self(
                    $action[WebuModuleActions::RAW_COL_CLASS],
                    $action[WebuModuleActions::RAW_COL_ACTION],
                    $action[WebuModuleActions::RAW_COL_CUSTOM_URL],
                    $action[WebuModuleActions::RAW_COL_IDENTIFIER],
                    $action[WebuModuleActions::RAW_COL_MODULE_ID],
                    $action[WebuModuleActions::RAW_COL_ID]
                );
            }
            return $moduleActions;
        }
    }


    public function equals(ModuleActionStorage $other) {
        return (
            $this->getClass() == $other->getClass() &&
            $this->getAction() == $other->getAction() &&
            $this->getModuleId() == $other->getModuleId()
        );
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
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getCustomUrl(): string
    {
        return $this->custom_url;
    }

    /**
     * @param string $custom_url
     */
    public function setCustomUrl(string $custom_url): void
    {
        $this->custom_url = $custom_url;
    }

    /**
     * @return int
     */
    public function getModuleId(): int
    {
        return $this->module_id;
    }

    /**
     * @param int $module_id
     */
    public function setModuleId(int $module_id): void
    {
        $this->module_id = $module_id;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }








}