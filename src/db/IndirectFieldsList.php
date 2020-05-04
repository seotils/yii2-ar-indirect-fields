<?php
declare(strict_types=1);

namespace seotils\yii2\db;

class IndirectFieldsList implements IndirectFieldListInterface
{
    private array $items;

    /**
     * IndirectFieldsList constructor.
     *
     * @param string $tableName
     * @param array  $items
     */
    public function __construct(string $tableName, array $items = [])
    {
        foreach ($items as $fieldName => $fieldType) {
            $this->items [$fieldName] = new IndirectField($tableName, $fieldName, $fieldType);
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return (function () {
            foreach ($this->items as $key => $val) {
                yield $key => $val;
            }
        })();
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return array_keys($this->items);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateSql(): ?string
    {
        /*  @var $item \seotils\yii2\db\IndirectFieldInterface */
        $result = [];
        foreach ($this->items as $item) {
            $sql = $item->getUpdateSql();
            if ($sql) {
                $result [] = $sql;
            }
        }

        return
            empty($result)
                ? null
                : implode(', ', $result);
    }

    public function clearChanges(): void
    {
        /*  @var $item \seotils\yii2\db\IndirectFieldInterface */
        foreach ($this->items as $item) {
            $item->clearChanges();
        }
    }

    /**
     * @inheritDoc
     */
    public function get($fieldName): IndirectFieldInterface
    {
        if (!in_array($fieldName, $this->getAttributes())) {
            throw new \InvalidArgumentException(sprintf(
                'Field `%s` does not exists.',
                $fieldName
            ));
        }

        return $this->items[$fieldName];
    }
}
