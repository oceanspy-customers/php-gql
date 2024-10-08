<?php

namespace Vertuoza\Usecases\Settings\UnitTypes;

use React\Promise\Promise;
use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Repositories\RepositoriesFactory;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

class UnitTypeByIdUseCase
{
  private UnitTypeRepository $unitTypeRepository;
  private UserRequestContext $userContext;
  public function __construct(
    RepositoriesFactory $repositories,
    UserRequestContext $userContext
  ) {
    $this->unitTypeRepository = $repositories->unitType;
    $this->userContext = $userContext;
  }

  /**
   * @param string $id id of the unit type to retrieve
   * @param int $page page of the unit type to retrieve
   * @return Promise<UnitTypeEntity>
   */
  public function handle(string $id): Promise
  {
    return $this->unitTypeRepository->getById($id, $this->userContext->getTenantId());
  }
}
