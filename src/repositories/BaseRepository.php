<?php

namespace Vertuoza\Repositories;

use Vertuoza\Repositories\Database\QueryBuilder;

abstract class BaseRepository
{

    public function __construct(
      protected QueryBuilder $db,
      protected string $tableName
    ) { }

    public function getQueryBuilder()
    {
        return $this->db->getConnection()->table($this->tableName);
    }
}