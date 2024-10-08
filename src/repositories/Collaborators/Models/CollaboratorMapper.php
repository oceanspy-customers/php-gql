<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators\Models;

use Vertuoza\Entities\Collaborators\CollaboratorEntity;
use Vertuoza\Repositories\Collaborators\CollaboratorMutationData;
use Vertuoza\Repositories\Interfaces\MapperInterface;
use Vertuoza\Repositories\Interfaces\ModelInterface;
use Vertuoza\Repositories\Interfaces\MutationDataInterface;

class CollaboratorMapper implements MapperInterface
{
    public static function modelToEntity(ModelInterface $dbData): CollaboratorEntity
    {
        $entity = new CollaboratorEntity();
        $entity->id = $dbData->id . '';
        $entity->name = $dbData->name;
        $entity->firstName = $dbData->first_name;

        return $entity;
    }

    public static function serializeUpdate(MutationDataInterface $mutation): array
    {
        return self::serializeMutation($mutation);
    }

    public static function serializeCreate(MutationDataInterface $mutation, string $tenantId): array
    {
        return self::serializeMutation($mutation, $tenantId);
    }

    private static function serializeMutation(MutationDataInterface $mutation, string $tenantId = null): array
    {
        $data = [
            'label' => $mutation->name,
        ];

        if ($tenantId) {
            $data[CollaboratorModel::getTenantColumnName()] = $tenantId;
        }
        return $data;
    }
}