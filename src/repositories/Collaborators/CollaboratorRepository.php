<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators;

use mysql_xdevapi\Warning;
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

  private const DEFAULT_PAGE_SIZE = 1;

  public function __construct(
      private QueryBuilder $database,
      PromiseAdapterInterface $dataLoaderPromiseAdapter
  ) {
      parent::__construct($database, CollaboratorModel::class);
      $this->dataLoaderPromiseAdapter = $dataLoaderPromiseAdapter;
      $this->getbyIdsDL = [];
  }

  private function fetchByIds(string $tenantId, array $ids, int $page = 0) : Promise
  {
    return async(function () use ($tenantId, $ids) {
      $query = $this->getQueryBuilder()
        ->where(function ($query) use ($tenantId) {
          $query->where([CollaboratorModel::getTenantColumnName() => $tenantId])
            ->orWhere(CollaboratorModel::getTenantColumnName(), null); // tenant_id must be set, so why return null as well ? Except if null = for all <> keeping the same behavior
        });
      $query->whereNull('deleted_at');
      $query->whereIn(CollaboratorModel::getPkColumnName(), $ids);

      $entities = $query->limit(self::DEFAULT_PAGE_SIZE)->offset($page * self::DEFAULT_PAGE_SIZE)->get()->mapWithKeys(function ($row) {
        $entity = CollaboratorMapper::modelToEntity(CollaboratorModel::fromStdclass($row));
        return [$entity->id => $entity];
      });

      // Map the IDs to the corresponding entities, preserving the order of IDs.
      return collect($ids)
        ->map(fn ($id) => $entities->get($id))
        ->toArray();
    })();
  }

  protected function getDataloader(string $tenantId, int $page = 0): DataLoader
  {
    if (!isset($this->getbyIdsDL[$tenantId])) {

      $dl = new DataLoader(function (array $ids) use ($tenantId, $page) {
        return $this->fetchByIds($tenantId, $ids, $page);
      }, $this->dataLoaderPromiseAdapter);
      $this->getbyIdsDL[$tenantId] = $dl;
    }

    return $this->getbyIdsDL[$tenantId];
  }

  public function getByIds(array $ids, string $tenantId): Promise
  {
    return $this->getDataloader($tenantId, 0)->loadMany($ids);
  }

  public function getById(string $id, string $tenantId, int $page = 0): Promise
  {
    return $this->getDataloader($tenantId, $page)->load($id);
  }

  public function findMany(string $tenantId, int $page = 0): Promise
  {
    return async(
      fn () => $this->getQueryBuilder()
        ->whereNull('deleted_at')
        ->where(CollaboratorModel::getTenantColumnName(), '=', $tenantId)
        ->limit(self::DEFAULT_PAGE_SIZE)
        ->offset($page * self::DEFAULT_PAGE_SIZE)
        ->get()
        ->map(function ($row) {
          return CollaboratorMapper::modelToEntity(CollaboratorModel::fromStdclass($row));
        })
    )();
  }
}
