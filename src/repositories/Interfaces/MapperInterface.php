<?php

namespace Vertuoza\Repositories\Interfaces;

interface MapperInterface
{
  public static function modelToEntity(ModelInterface $dbData);
  public static function serializeUpdate(MutationDataInterface $mutation): array;
  public static function serializeCreate(MutationDataInterface $mutation, string $tenantId): array;
}