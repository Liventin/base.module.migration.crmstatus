<?php

namespace Base\Module\Install;

use Base\Module\Install\Interface\Install;
use Base\Module\Install\Interface\ReInstall;
use Base\Module\Service\Container;
use Base\Module\Service\Migration\CrmStatus\CrmStatusEntity;
use Base\Module\Service\Migration\CrmStatus\CrmStatusService as ICrmStatusService;
use Base\Module\Service\Tool\ClassList;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\SystemException;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class CrmStatusInstaller implements Install, ReInstall
{
    /**
     * @return array
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    private function getEntities(): array
    {
        /** @var ClassList $classList */
        $classList = Container::get(ClassList::SERVICE_CODE);
        return $classList->setSubClassesFilter([CrmStatusEntity::class])->getFromLib('Migration');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function install(): void
    {
        /** @var ICrmStatusService $crmStatusService */
        $crmStatusService = Container::get(ICrmStatusService::SERVICE_CODE);
        $crmStatusService->setEntities($this->getEntities())->install();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function reInstall(): void
    {
        /** @var ICrmStatusService $crmStatusService */
        $crmStatusService = Container::get(ICrmStatusService::SERVICE_CODE);
        $crmStatusService->setEntities($this->getEntities())->reInstall();
    }

    public function getInstallSort(): int
    {
        return 500;
    }

    public function getReInstallSort(): int
    {
        return 500;
    }
}
