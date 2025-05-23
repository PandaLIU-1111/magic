<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace App\Application\KnowledgeBase\Service\Strategy\KnowledgeBase;

use App\Application\Kernel\AbstractKernelAppService;
use App\Application\Permission\Service\OperationPermissionAppService;
use App\Domain\KnowledgeBase\Entity\KnowledgeBaseEntity;
use App\Domain\KnowledgeBase\Entity\ValueObject\KnowledgeBaseDataIsolation;
use App\Domain\KnowledgeBase\Entity\ValueObject\KnowledgeType;
use App\Domain\KnowledgeBase\Service\KnowledgeBaseDocumentDomainService;
use App\Domain\Permission\Entity\ValueObject\OperationPermission\Operation;
use App\Domain\Permission\Entity\ValueObject\OperationPermission\ResourceType;

class BaseKnowledgeBaseStrategy extends AbstractKernelAppService implements KnowledgeBaseStrategyInterface
{
    public function __construct(
        protected OperationPermissionAppService $operationPermissionAppService,
        protected KnowledgeBaseDocumentDomainService $knowledgeBaseDocumentDomainService,
    ) {
    }

    /**
     * @return array<string, Operation>
     */
    public function getKnowledgeBaseOperations(KnowledgeBaseDataIsolation $dataIsolation): array
    {
        $permissionDataIsolation = $this->createPermissionDataIsolation($dataIsolation);
        return $this->operationPermissionAppService->getResourceOperationByUserIds(
            $permissionDataIsolation,
            ResourceType::Knowledge,
            [$dataIsolation->getCurrentUserId()]
        )[$dataIsolation->getCurrentUserId()] ?? [];
    }

    public function getQueryKnowledgeTypes(): array
    {
        return [KnowledgeType::UserKnowledgeBase->value];
    }

    public function getKnowledgeOperation(KnowledgeBaseDataIsolation $dataIsolation, int|string $knowledgeCode): Operation
    {
        $permissionDataIsolation = $this->createPermissionDataIsolation($dataIsolation);

        if (empty($knowledgeCode)) {
            return Operation::None;
        }
        return $this->operationPermissionAppService->getOperationByResourceAndUser(
            $permissionDataIsolation,
            ResourceType::Knowledge,
            (string) $knowledgeCode,
            $permissionDataIsolation->getCurrentUserId()
        );
    }

    public function getOrCreateDefaultDocument(KnowledgeBaseDataIsolation $dataIsolation, KnowledgeBaseEntity $knowledgeBaseEntity): void
    {
        $this->knowledgeBaseDocumentDomainService->getOrCreateDefaultDocument($dataIsolation, $knowledgeBaseEntity);
    }
}
