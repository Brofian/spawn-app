<?php

namespace spawnCore\Database\Entity\TableDefinition;

use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypeOptions;

abstract class AbstractColumn {

    protected array $uniqueColumnCombination = [];

    abstract public function getName(): string;

    abstract public function getType(): string;

    /**
     * @return string|int
     */
    abstract public function getTypeIdentifier();

    public function isUnique(): bool {
        return !empty($this->getCombinedUniqueColumns());
    }

    public function getCombinedUniqueColumns(): array {
        return $this->uniqueColumnCombination;
    }

    /**
     * Pass a falsy value for no unique Key,
     * pass a positive value for this column to be a unique key of its own
     * pass a string to create a combined unique key from this column and the passed column
     * pass an array to freely define multiple columns for a constraint combination
     *
     * @param bool|string|array|null $columns
     */
    protected function setUniqueCombinedColumns($columns): void {
        if(!$columns) {
            return;
        }

        if(is_string($columns)) {
            $this->uniqueColumnCombination = [$this->columnName, $columns];
            return;
        }
        elseif(is_array($columns)) {
            $this->uniqueColumnCombination = $columns;
            return;
        }

        $this->uniqueColumnCombination = [$this->columnName];
    }


    public function isPrimaryKey(): bool {
        return false;
    }

    public function getForeignKeyConstraint(): ?ForeignKey {
        return null;
    }

    /**
     * @return string|int|null
     */
    public function getDefault() {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::DEFAULT];
    }

    protected function canBeNull(): ?bool {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::NOTNULL];
    }

    protected function isUnsigned(): ?bool {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::UNSIGNED];
    }

    protected function isAutoIncrement(): ?bool {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::AUTOINCREMENT];
    }

    protected function getLength(): ?int {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::LENGTH];
    }

    protected function hasFixedLength(): ?bool {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::FIXED];
    }

    protected function getPrecision(): ?int {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::PRECISION];
    }

    protected function getScale(): ?int {
        return ColumnTypeOptions::OPTION_DEFAULTS[ColumnTypeOptions::SCALE];
    }

    public function getOptions(bool $keepNullValues = false): array {

        $default = $this->getDefault();
        if(is_string($default)) {
            $default = "'$default'";
        }

        $givenOptions = [
            'notnull' => !$this->canBeNull(),
            'length' => $this->getLength(),
            'unsigned' => $this->isUnsigned(),
            'default' => $default,
            'autoincrement' => $this->isAutoIncrement(),
            'fixed' => $this->hasFixedLength(),
            'precision' => $this->getPrecision(),
            'scale' => $this->getScale()
        ];

        $requiredOptions = ColumnTypeOptions::getOptionsForType($this->getType());

        $options = [];
        foreach($requiredOptions as $optionKey) {
            if(
                isset($givenOptions[$optionKey]) &&
                ($keepNullValues || $givenOptions[$optionKey] !== null)
            ) {
                $options[$optionKey] = $givenOptions[$optionKey];
            }
        }

        return $options;
    }

}