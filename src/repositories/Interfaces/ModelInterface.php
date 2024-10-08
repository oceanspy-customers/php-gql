<?php

namespace Vertuoza\Repositories\Interfaces;

use stdClass;

interface ModelInterface
{
  public static function fromStdclass(stdClass $data): ModelInterface;
  public static function getPkColumnName(): string;
  public static function getTenantColumnName(): string;
  public static function getTableName(): string;
}