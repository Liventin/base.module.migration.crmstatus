# base.module.migration.crmstatus

<table>
<tr>
<td>
<a href="https://github.com/Liventin/base.module">Bitrix Base Module</a>
</td>
</tr>
</table>

install | update

```
"require": {
    "liventin/base.module.migration.crmstatus": "^1.0.0"
}
```
redirect (optional)
```
"extra": {
  "service-redirect": {
    "liventin/base.module.migration.crmstatus": "module.name",
  }
}
```
PhpStorm Live Template
```php
<?php

namespace ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Migration\CrmStatus;


use ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Service\Migration\CrmStatus\CrmStatusEntity;

class SampleStatus implements CrmStatusEntity
{

    public static function getEntityId(): string
    {
        return 'DEAL_TYPE';
    }

    public static function getCode(): string
    {
        return 'CUSTOM_DEAL';
    }

    public static function getName(): string
    {
        return 'Проверка';
    }

    public static function getSort(): int
    {
        return 100;
    }
}
```