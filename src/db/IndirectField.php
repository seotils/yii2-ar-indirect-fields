<?php
declare(strict_types=1);

namespace seotils\yii2\db;

class IndirectField implements IndirectFieldInterface
{
    private string $tableName;

    private string $fieldName;

    private string $fieldType;

    private float $value;

    /**
     * IndirectField constructor.
     *
     * @param string $tableName
     * @param string $fieldName
     * @param string $fieldType
     */
    public function __construct(string $tableName, string $fieldName, string $fieldType)
    {
        if (!in_array($fieldType, [
            IndirectFieldInterface::TYPE_INT,
            IndirectFieldInterface::TYPE_FLOAT,
        ])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid type `%s` of indirectly modified field `%s`.',
                    $fieldType,
                    $fieldName
                )
            );
        }
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->value     = 0;
    }

    /**
     * @inheritDoc
     */
    public function incrBy($value): self
    {
        $this->value +=
            IndirectFieldInterface::TYPE_INT == $this->fieldType
                ? (int)$value
                : (float)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function decrBy($value): self
    {
        $this->value -=
            IndirectFieldInterface::TYPE_INT == $this->fieldType
                ? (int)$value
                : (float)$value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return
            IndirectFieldInterface::TYPE_INT == $this->fieldType
                ? (int)$this->value
                : (float)$this->value;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateSql(): ?string
    {
        if (!$this->changed()) {
            return null;
        }

        $value = $this->getValue();
        $sign  = $value < 0 ? '-' : '+';

        return "{$this->tableName}.{$this->fieldName} = {$this->tableName}.{$this->fieldName} {$sign} ".abs($value);
    }

    /**
     * @inheritDoc
     */
    public function changed(): bool
    {
        return $this->value !== 0;
    }

    /**
     * @inheritDoc
     */
    public function clearChanges(): self
    {
        $this->value = 0;

        return $this;
    }
}
