<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators;

use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Promise\Promise;
use Vertuoza\Repositories\BaseRepository;
use Vertuoza\Repositories\Database\QueryBuilder;
use Vertuoza\Repositories\Collaborators\Models\CollaboratorMapper;
use Vertuoza\Repositories\Collaborators\Models\CollaboratorModel;
use Vertuoza\Repositories\Collaborators\CollaboratorMutationData;

use function React\Async\async;

class CollaboratorRepository extends BaseRepository
{
  protected array $getbyIdsDL;
  protected PromiseAdapterInterface $dataLoaderPromiseAdapter;

  public function __construct(
      private QueryBuilder $database,
      PromiseAdapterInterface $dataLoaderPromiseAdapter
  ) {
      parent::__construct($database, CollaboratorModel::getTableName());
      $this->dataLoaderPromiseAdapter = $dataLoaderPromiseAdapter;
      $this->getbyIdsDL = [];
  }

  public function findMany(string $tenantId)
  {
    return async(
      fn () => $this->getQueryBuilder()
        ->whereNull('deleted_at')
        ->where(function ($query) use ($tenantId) {
          $query->where(CollaboratorModel::getTenantColumnName(), '=', $tenantId)
            ->orWhereNull(CollaboratorModel::getTenantColumnName());
        })
        ->get()
        ->map(function ($row) {
          return CollaboratorMapper::modelToEntity(CollaboratorModel::fromStdclass($row));
        })
    )();
  }

  // TODO: Put it in BaseRepository

  public function getByIds(array $ids, string $tenantId): Promise
  {
    return $this->getDataloader($tenantId)->loadMany($ids);
  }

  public function getById(string $id, string $tenantId): Promise
  {
    return $this->getDataloader($tenantId)->load($id);
  }

  private function fetchByIds(string $tenantId, array $ids)
  {
    return async(function () use ($tenantId, $ids) {
      $query = $this->getQueryBuilder()
        ->where(function ($query) use ($tenantId) {
          $query->where([CollaboratorModel::getTenantColumnName() => $tenantId])
            ->orWhere(CollaboratorModel::getTenantColumnName(), null);
        });
      $query->whereNull('deleted_at');
      $query->whereIn(CollaboratorModel::getPkColumnName(), $ids);

      $entities = $query->get()->mapWithKeys(function ($row) {
        $entity = CollaboratorMapper::modelToEntity(CollaboratorModel::fromStdclass($row));
        return [$entity->id => $entity];
      });

      // Map the IDs to the corresponding entities, preserving the order of IDs.
      return collect($ids)
        ->map(fn ($id) => $entities->get($id))
        ->toArray();
    })();
  }

  protected function getDataloader(string $tenantId): DataLoader
  {
    if (!isset($this->getbyIdsDL[$tenantId])) {

      $dl = new DataLoader(function (array $ids) use ($tenantId) {
        return $this->fetchByIds($tenantId, $ids);
      }, $this->dataLoaderPromiseAdapter);
      $this->getbyIdsDL[$tenantId] = $dl;
    }

    return $this->getbyIdsDL[$tenantId];
  }

}