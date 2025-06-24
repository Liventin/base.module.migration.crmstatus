<?php

namespace Base\Module\Service\Migration\CrmStatus;

interface CrmStatusKanbanEntity
{
    public static function getCategoryId(): int;
    public static function getColor(): ?string;
    public static function getSemantics(): ?string;
}