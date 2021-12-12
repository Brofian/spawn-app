<?php

namespace spawnCore\Database\Entity\TableDefinition;

use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypeOptions;

abstract class AbstractColumn {

    abstract public function getName(): string;

    abstract public function getType(): string;

    /**
     * @return string|int
     */
    abstract public function getTypeIdentifier();

    public function isUnique(): bool {
        return false;
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