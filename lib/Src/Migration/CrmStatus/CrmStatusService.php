<?php

namespace Base\Module\Src\Migration\CrmStatus;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Base\Module\Service\Migration\CrmStatus\CrmStatusService as ICrmStatusService;
use Base\Module\Service\LazyService;
use CCrmStatus;

#[LazyService(serviceCode: ICrmStatusService::SERVICE_CODE, constructorParams: [])]
class CrmStatusService implements ICrmStatusService
{
    private array $entities = [];

    public function setEntities(array $entities): self
    {
        $this->entities = $entities;
        return $this;
    }

    /**
     * @throws SystemException
     * @throws LoaderException
     */
    public function install(): void
    {
        Loader::requireModule('crm');
        
        foreach ($this->entities as $entityClass) {
            if (!class_exists($entityClass)) {
                continue;
            }

            $entityId = $entityClass::getEntityId();
            $code = $entityClass::getCode();
            $name = $entityClass::getName();
            $sort = $entityClass::getSort();
            
            if (empty($entityId) || empty($code) || empty($name)) {
                continue;
            }

            $status = new CCrmStatus($entityId);
            $existing = $status::GetStatusList($entityId);

            if (isset($existing[$code])) {
                continue;
            }

            $status->Add([
                'ENTITY_ID' => $entityId,
                'STATUS_ID' => $code,
                'NAME' => $name,
                'SORT' => $sort,
                'SYSTEM' => 'N',
            ]);
        }
    }

    /**
     * @throws SystemException
     * @throws LoaderException
     */
    public function reInstall(): void
    {
        $this->install();
    }
}
