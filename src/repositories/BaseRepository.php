<?php

namespace Vertuoza\Repositories;

use Vertuoza\Repositories\Database\QueryBuilder;
use Illuminate\Database\Query\Builder;

abstract class BaseRepository
{

  public function __construct(
    protected QueryBuilder $db,
    protected string $modelClass,
  ) { }

  public function getQueryBuilder(): Builder
  {
    return $this->db->getConnection()->table($this->modelClass::getTableName());
  }
}