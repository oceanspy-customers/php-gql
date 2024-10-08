<?php

namespace Vertuoza\Repositories;

use Vertuoza\Repositories\Database\QueryBuilder;
use function React\Async\async;
use React\Promise\Promise;

abstract class BaseRepository
{

  public function __construct(
    protected QueryBuilder $db,
    protected string $modelClass,
  ) { }

  public function getQueryBuilder()
  {
      return $this->db->getConnection()->table($this->modelClass::getTableName());
  }
}