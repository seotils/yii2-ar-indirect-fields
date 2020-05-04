<?php
declare(strict_types=1);

namespace seotils\yii2\db;

interface IndirectFieldListInterface extends \IteratorAggregate
{
    /**
     * Returns indirectly modifiable field.
     *
     * @param $fieldName
     *
     * @return \seotils\yii2\db\IndirectFieldInterface
     */
    public function get($fieldName): IndirectFieldInterface;

    /**
     * List of fields names in collection.
     *
     * @return string[]
     */
    public function getAttributes(): array;

    /**
     * Returns all SET parts of UPDATE SQL syntax.
     * Example: "filed1 = filed1 + 12, filed2 = filed2 + 12, ..."
     *
     * @return string
     */
    public function getUpdateSql(): ?string;

    /**
     * Clears all changes in fields.
     */
    public function clearChanges(): void;

}
