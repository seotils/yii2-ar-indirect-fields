<?php
declare(strict_types=1);

namespace seotils\yii2\db;

interface IndirectFieldInterface
{
    public const TYPE_INT   = 'int';
    public const TYPE_FLOAT = 'float';

    /**
     * Increments field  by value.
     *
     * @param int|float $value
     *
     * @return self
     */
    public function incrBy($value): self;

    /**
     * Decrements field  by value.
     *
     * @param int|float $value
     *
     * @return self
     */
    public function decrBy($value): self;

    /**
     * Returns a change in the value of a field.
     *
     * @return int|float
     */
    public function getValue();

    /**
     * Returns TRUE if the field value has been changed, in other case FALSE.
     *
     * @return bool
     */
    public function changed(): bool;

    /**
     * Returns a SET part of UPDATE SQL syntax for field.
     * Example: "filed1 = filed1 + 12"
     *
     * @return string|null
     */
    public function getUpdateSql(): ?string;

    /**
     * Clear field changes.
     *
     * @return $this
     */
    public function clearChanges(): self;

}
