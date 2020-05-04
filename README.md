# ActiveRecordIndirectFields package for yii2 framework.

## Overview.

This package makes it possible to make changes of some fields
relative to them itselfs. For example, in hiload projects fields
like `views_count` or `сlicks_count` can be updated
incorrectly due to the fact that during concurrent requests
the data of these fields become outdated.

## Requirements

1. php >= 7.4.0.
2. yii2 installed.

In some IDE\`s, you may need to modify `/yii2-project/root/folder/composer.json`:

```json
{
    ...,
    "require": {
        "php": ">=7.4.0",
        ...,
    },
    ...,
}
```
 
## Installation.

```bash
$ cd /yii2-project/root/folder
$ composer update
$ composer require seotils/yii2-ar-indirect-fields
```

## Usage.

At first, create a model:

```php
<?php
declare(strict_types=1);

namespace common\models;

use seotils\yii2\db\ActiveRecordIndirectFields;
use seotils\yii2\db\IndirectFieldInterface;

/**
 * Test pk single model
 *
 * @property int     $id
 * @property int     $field_int
 * @property float   $field_float
 */
class TestIndirectPkSingle extends ActiveRecordIndirectFields
{
    /**
     * @inheritDoc
     */
    public static function indirectFields(): array
    {
        return [
            'field_int'   => IndirectFieldInterface::TYPE_INT,
            'field_float' => IndirectFieldInterface::TYPE_FLOAT,
        ];
    }
}
```

Now, we are test it:

```php
<?php
declare(strict_types=1);

namespace your\anyNamespace;

use common\models\TestIndirectPkSingle;

/* 
 * All right, because it`s a new record.
 */
$modelPkSingle              = new TestIndirectPkSingle();
$modelPkSingle->field_int   = 12;
$modelPkSingle->field_float = 1.2;
var_dump($modelPkSingle->save());

/* 
 * Raise an exception, because it`s direct change of existing record.
 */
// $modelPkSingle->field_int = 13;
// $modelPkSingle->field_float = 1.3;
// var_dump($modelPkSingle->save());

/* 
 * All right, because it`s a indirect change of a record.
 */
$modelPkSingle->indirectField('field_int')->incrBy(1);
$modelPkSingle->indirectField('field_float')->decrBy(.1);
var_dump($modelPkSingle->save());
```

With best reguards, Nickolay Lubyshev.
