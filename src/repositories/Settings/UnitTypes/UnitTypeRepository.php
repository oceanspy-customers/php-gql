<?php

namespace Vertuoza\Repositories\Settings\UnitTypes;

use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\PromiseAdapterInterface;
use React\Promise\Promise;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;
use Vertuoza\Repositories\BaseRepository;
use Vertuoza\Repositories\Database\QueryBuilder;
use Vertuoza\Repositories\Interfaces\MutationDataInterface;
use Vertuoza\Repositories\Settings\UnitTypes\Models\UnitTypeMapper;
use Vertuoza\Repositories\Settings\UnitTypes\Models\UnitTypeModel;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeMutationData;
use Ramsey\Uuid\Uuid;

use function React\Async\async;

class UnitTypeRepository extends BaseRepository
{
  protected array $getbyIdsDL;
  protected PromiseAdapterInterface $dataLoaderPromiseAdapter;

  private const DEFAULT_PAGE_SIZE = 10;

  public function __construct(
    private QueryBuilder $database,
    PromiseAdapterInterface $dataLoaderPromiseAdapter
  ) {
    parent::__construct($database, UnitTypeModel::class);
    $this->dataLoaderPromiseAdapter = $dataLoaderPromiseAdapter;
    $this->getbyIdsDL = [];
  }

  private function fetchByIds(string $tenantId, array $ids, int $page = 0) : Promise
  {
    return async(function () use ($tenantId, $ids) {
      $query = $this->getQueryBuilder()
        ->where(function ($query) use ($tenantId) {
          $query->where([UnitTypeModel::getTenantColumnName() => $tenantId])
            ->orWhere(UnitTypeModel::getTenantColumnName(), null);
        });
      $query->whereNull('deleted_at');
      $query->whereIn(UnitTypeModel::getPkColumnName(), $ids);

      $entities = $query->limit(self::DEFAULT_PAGE_SIZE)->offset($page * self::DEFAULT_PAGE_SIZE)->get()->mapWithKeys(function ($row) {
        $entity = UnitTypeMapper::modelToEntity(UnitTypeModel::fromStdclass($row));
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

  public function getByIds(array $ids, string $tenantId, int $page = 0): Promise
  {
    return $this->getDataloader($tenantId, $page)->loadMany($ids);
  }

  public function getById(string $id, string $tenantId): Promise
  {
    return $this->getDataloader($tenantId, 0)->load($id);
  }

  public function countUnitTypeWithLabel(string $name, string $tenantId, string|int|null $excludeId = null): Promise
  {
    return async(
      fn () => $this->getQueryBuilder()
        ->where('label', $name)
        ->whereNull('deleted_at')
        ->where(function ($query) use ($excludeId) {
          if (isset($excludeId))
            $query->where('id', '!=', $excludeId);
        })
        ->where(UnitTypeModel::getTenantColumnName(), '=', $tenantId)
    )();
  }

  public function findMany(string $tenantId, int $page = 0): Promise
  {
    return async(
      fn () => $this->getQueryBuilder()
        ->whereNull('deleted_at')
        ->where(UnitTypeModel::getTenantColumnName(), '=', $tenantId)
        ->limit(self::DEFAULT_PAGE_SIZE)
        ->offset($page * self::DEFAULT_PAGE_SIZE)
        ->get()
        ->map(function ($row) {
          return UnitTypeMapper::modelToEntity(UnitTypeModel::fromStdclass($row));
        })
    )();
  }

  public function create(MutationDataInterface $data, string $tenantId): string
  {
    try {
      $uuid = Uuid::uuid4()->toString();
      $serializedData = UnitTypeMapper::serializeCreate($data, $tenantId);
      $serializedData['id'] = $uuid;
      $this->getQueryBuilder()->insert($serializedData);
    } catch (\Exception $e) {
      ApplicationLogger::getInstance()->error($e, 'CREATE_UNIT_TYPE', new LogContext(null));
    }

    return $uuid;
  }

  public function update(string $id, MutationDataInterface $data): void
  {
    $this->getQueryBuilder()
      ->where(UnitTypeModel::getPkColumnName(), $id)
      ->update(UnitTypeMapper::serializeUpdate($data));

    $this->clearCache($id);
  }

  private function clearCache(string $id): void
  {
    foreach ($this->getbyIdsDL as $dl) {
      if ($dl->key_exists($id)) {
        $dl->clear($id);
        return;
      }
    }
  }
}
