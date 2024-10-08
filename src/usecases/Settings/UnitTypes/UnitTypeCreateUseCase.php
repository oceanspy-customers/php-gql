<?php

namespace Vertuoza\Usecases\Settings\UnitTypes;

use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Repositories\RepositoriesFactory;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeMutationData;
use Vertuoza\Repositories\Settings\UnitTypes\UnitTypeRepository;

class UnitTypeCreateUseCase
{
  private UnitTypeRepository $unitTypeRepository;
  private UserRequestContext $userContext;

  private const MAX_NAME_LENGTH = 255;

  public function __construct(
    RepositoriesFactory $repositories,
    UserRequestContext $userContext
  ) {
    $this->unitTypeRepository = $repositories->unitType;
    $this->userContext = $userContext;
  }

  /**
   * @param string $name name of the unit type to create
   * @return void
   */
  public function handle(string $name): string
  {
    try {
      $mutationData = new UnitTypeMutationData();
      $mutationData->name = $this->sanitizeString($name);
      $newId = $this->unitTypeRepository->create($mutationData, $this->userContext->getTenantId());
    }
    catch (\Exception $e) {
      throw new \Exception('Failed to create unit type');
    }

    return $newId;
  }

  private function sanitizeString(string $string): string
  {
    // TODO: decide how much we want to sanitize the input

    $string = trim($string);
    $string = strip_tags($string);

    if (strlen($string) > self::MAX_NAME_LENGTH) {
      $string = substr($string, 0, 255);
    }

    return $string;
  }

}
