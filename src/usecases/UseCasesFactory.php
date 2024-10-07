<?php

namespace Vertuoza\Usecases;

use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Usecases\Collaborators\CollaboratorUseCases;
use Vertuoza\Usecases\Settings\UnitTypes\UnitTypeUseCases;
use Vertuoza\Repositories\RepositoriesFactory;

class UseCasesFactory
{
  public UnitTypeUseCases $unitType;
  public CollaboratorUseCases $collaborator;

  public function __construct(UserRequestContext $userContext, RepositoriesFactory $repositories)
  {
    $this->unitType = new UnitTypeUseCases($userContext, $repositories);
    $this->collaborator = new CollaboratorUseCases($userContext, $repositories);
  }
}
