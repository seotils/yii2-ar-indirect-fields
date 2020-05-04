<?php
declare(strict_types=1);

namespace seotils\yii2\db;

use yii\db\ActiveRecord;

abstract class ActiveRecordIndirectFields extends ActiveRecord
{
    private ?IndirectFieldListInterface $indirectFields = null;

    /**
     * Returns a key-value pairs with indirectly modifiable fields names and types:
     *     [
     *         'field1' => \seotils\yii2\db\IndirectFieldInterface::TYPE_INT,
     *         'field2' => \seotils\yii2\db\IndirectFieldInterface::TYPE_FLOAT,
     *         ...
     *     ]
     *
     * @return array
     */
    abstract public static function indirectFields(): array;

    /**
     * Returns indirectly modifiable field.
     *
     * @param string $fieldName
     *
     * @return \seotils\yii2\db\IndirectFieldInterface|null
     */
    public function indirectField(string $fieldName): ?IndirectFieldInterface
    {
        return $this->getIndirectFields()->get($fieldName);
    }

    /**
     * Returns indirectly modifiable fields list.
     *
     * @return \seotils\yii2\db\IndirectFieldListInterface
     */
    public function getIndirectFields(): IndirectFieldListInterface
    {
        if (!$this->indirectFields) {
            $this->indirectFields = new IndirectFieldsList(
                static::tableName(),
                static::indirectFields());
        }

        return $this->indirectFields;
    }

    /**
     * @inheritDoc
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if (!$this->isNewRecord && !empty($this->getIndirectFields())) {
            $invalidFields = $this->getDirtyAttributes($this->getIndirectFields()->getAttributes());
            if (!empty($invalidFields)) {
                throw new \yii\db\Exception(
                    'Fields `'.implode('`, `', array_keys($invalidFields)).
                    '` of `'.static::tableName().'` table cannot be changed directly.'
                );
            }
        }

        $result = parent::update($runValidation, $attributeNames);
        $result += $this->updateIndirectFields();

        return $result;
    }

    protected function updateIndirectFields(): bool
    {
        $result = true;
        if (!$this->isNewRecord && !empty($this->getIndirectFields())) {
            $updateSql = $this->getIndirectFields()->getUpdateSql();
            if ($updateSql) {
                $db       = static::getDb();
                $table    = static::tableName();
                $pk       = $this->getPrimaryKey(true);
                $pkSql    = [];
                $pkValues = [];
                foreach ($pk as $key => $value) {
                    $pkValues[':'.$key] = $value;
                    $pkSql[]            = "{$table}.{$key} = :{$key}";
                }
                /** @noinspection SqlNoDataSourceInspection */
                $cmd    = $db->createCommand(
                    sprintf("UPDATE %s SET %s WHERE (%s)",
                        $table,
                        $updateSql,
                        implode(' AND ', $pkSql))
                );
                $result = (bool)$cmd
                    ->bindValues($pkValues)
                    ->execute();
                if ($result) {
                    $this->getIndirectFields()->clearChanges();
                }
            }
        }

        return $result;
    }

}
