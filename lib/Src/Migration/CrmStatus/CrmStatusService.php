<?php

namespace Base\Module\Src\Migration\CrmStatus;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Base\Module\Service\Migration\CrmStatus\CrmStatusEntity;
use Base\Module\Service\Migration\CrmStatus\CrmStatusService as ICrmStatusService;
use Base\Module\Service\LazyService;
use CCrmStatus;

#[LazyService(serviceCode: ICrmStatusService::SERVICE_CODE, constructorParams: [])]
class CrmStatusService implements ICrmStatusService
{
    private array $entities = [];

    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('crm');
    }

    public function setEntities(array $entities): self
    {
        $this->entities = $entities;
        return $this;
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws NotFoundExceptionInterface
     * @throws NotSupportedException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws ReflectionException
     * @throws SystemException
     * @throws Exception
     */
    public function install(): void
    {
        $statusByEntity = $this->prepareStatusesList();

        foreach ($statusByEntity as $entityId => $statusClasses) {
            $crmStatus = new CCrmStatus($entityId);
            $existing = $crmStatus::GetStatus($entityId);

            foreach ($statusClasses as $class) {
                $code = $class::getCode();
                if (isset($existing[$code])) {
                    $updateFields = $this->prepareUpdateFields($existing, $class);
                    if (!empty($updateFields)) {
                        $crmStatus->Update($existing[$code]['ID'], $updateFields, ['ENABLE_NAME_INIT' => true]);
                    }
                } else {
                    $crmStatus->Add($this->prepareAddFields($class));
                }
            }
        }
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws NotFoundExceptionInterface
     * @throws NotSupportedException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function reInstall(): void
    {
        $this->install();
    }

    public function prepareStatusesList(): array
    {
        $statuses = [];
        foreach ($this->entities as $entityClass) {
            if (!class_exists($entityClass)) {
                continue;
            }

            $entityId = $entityClass::getEntityId();
            $code = $entityClass::getCode();
            $name = $entityClass::getName();

            if (empty($entityId) || empty($code) || empty($name)) {
                continue;
            }

            $statuses[$entityId][] = $entityClass;
        }
        return $statuses;
    }

    /**
     * @param array $existing
     * @param CrmStatusEntity $statusClass
     * @return array
     * @noinspection PhpDocSignatureInspection
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    private function prepareUpdateFields(array $existing, string $statusClass): array
    {
        $fields = [];

        $fieldValue = $statusClass::getSort();
        if ((int)$existing['SORT'] !== $fieldValue) {
            $fields['SORT'] = $fieldValue;
        }

        $fieldValue = $statusClass::getName();
        if ($existing['NAME_INIT'] !== $fieldValue) {
            $fields['NAME_INIT'] = $fieldValue;
            if ($existing['NAME_INIT'] === $existing['NAME']) {
                $fields['NAME'] = $fields['NAME_INIT'];
            }
        }

        if (method_exists($statusClass, 'getCategoryId')) {
            $fieldValue = $statusClass::getCategoryId();
            if ($existing['CATEGORY_ID'] !== $fieldValue) {
                $fields['CATEGORY_ID'] = $fieldValue;
            }
        }

        if (method_exists($statusClass, 'getColor')) {
            $fieldValue = $statusClass::getColor();
            if ($existing['COLOR'] !== $fieldValue) {
                $fields['COLOR'] = $fieldValue;
            }
        }

        if (method_exists($statusClass, 'getSemantics')) {
            $fieldValue = $statusClass::getSemantics();
            if ($existing['SEMANTICS'] !== $fieldValue) {
                $fields['SEMANTICS'] = $fieldValue;
            }
        }

        return $fields;
    }

    /**
     * @param CrmStatusEntity $statusClass
     * @return array
     * @noinspection PhpDocSignatureInspection
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function prepareAddFields(string $statusClass): array
    {
        $fields = [
            'ENTITY_ID' => $statusClass::getEntityId(),
            'STATUS_ID' => $statusClass::getCode(),
            'NAME' => $statusClass::getName(),
            'NAME_INIT' => $statusClass::getName(),
            'SORT' => $statusClass::getSort(),
            'SYSTEM' => 'N',
        ];

        if (method_exists($statusClass, 'getCategoryId')) {
            $fields['CATEGORY_ID'] = $statusClass::getCategoryId();
        }

        if (method_exists($statusClass, 'getColor')) {
            $fields['COLOR'] = $statusClass::getColor();
        }

        if (method_exists($statusClass, 'getSemantics')) {
            $fields['SEMANTICS'] = $statusClass::getSemantics();
        }

        return $fields;
    }

}
