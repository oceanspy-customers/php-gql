<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators\Models;

use Vertuoza\Entities\Collaborators\CollaboratorEntity;
use Vertuoza\Repositories\Collaborators\CollaboratorMutationData;

class CollaboratorMapper
{
    public static function modelToEntity(CollaboratorModel $dbData): CollaboratorEntity
    {
        $entity = new CollaboratorEntity();
        $entity->id = $dbData->id . '';
        $entity->name = $dbData->name;
        $entity->firstName = $dbData->first_name;

        return $entity;
    }

    public static function serializeUpdate(CollaboratorMutationData $mutation): array
    {
        return self::serializeMutation($mutation);
    }

    public static function serializeCreate(CollaboratorMutationData $mutation, string $tenantId): array
    {
        return self::serializeMutation($mutation, $tenantId);
    }

    private static function serializeMutation(CollaboratorMutationData $mutation, string $tenantId = null): array
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