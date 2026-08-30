<?php

namespace Base\Module\Service\Migration\CrmStatus;

interface CrmStatusService
{
    public const SERVICE_CODE = 'base.module.migration.crmstatus.service';

    public function setEntities(array $entities): self;
    public function install(): void;
    public function reInstall(): void;
}
