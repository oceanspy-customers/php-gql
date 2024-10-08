<?php

namespace Vertuoza\Repositories\Settings\UnitTypes\Models;

use stdClass;
use Vertuoza\Repositories\Interfaces\MapperInterface;
use Vertuoza\Repositories\Interfaces\ModelInterface;
use Vertuoza\Repositories\Interfaces\MutationDataInterface;
use Vertuoza\Repositories\Settings\UnitTypes\Models\UnitTypeModel;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeMutationData;
use Vertuoza\Entities\Settings\UnitTypeEntity;

class UnitTypeMapper implements MapperInterface
{
  public static function modelToEntity(ModelInterface $dbData): UnitTypeEntity
  {
    $entity = new UnitTypeEntity();
    $entity->id = $dbData->id . '';
    $entity->name = $dbData->label;
    $entity->isSystem = $dbData->tenant_id === null;

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
      $data[UnitTypeModel::getTenantColumnName()] = $tenantId;
    }
    return $data;
  }
}
