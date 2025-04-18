<?php

namespace Base\Module\Service\Migration\CrmStatus;

interface CrmStatusEntity
{
    public static function getEntityId(): string;
    public static function getCode(): string;
    public static function getName(): string;
    public static function getSort(): int;
}