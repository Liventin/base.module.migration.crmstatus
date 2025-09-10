<?php

namespace Base\Module\Service\Migration\CrmStatus;

interface CrmStatusKanbanEntity extends CrmStatusEntity
{
    public static function getCategoryId(): int;
    public static function getColor(): ?string;
    public static function getSemantics(): ?string;
}