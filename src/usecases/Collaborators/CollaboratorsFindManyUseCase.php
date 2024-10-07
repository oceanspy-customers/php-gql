<?php

namespace Vertuoza\Usecases\Collaborators;

use React\Promise\Promise;
use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Entities\Collaborators\CollaboratorEntity;
use Vertuoza\Repositories\Collaborators\CollaboratorRepository;
use Vertuoza\Repositories\RepositoriesFactory;

class CollaboratorsFindManyUseCase
{
  private UserRequestContext $userContext;
  private CollaboratorRepository $collaboratorRepository;

  public function __construct(
    RepositoriesFactory $repositories,
    UserRequestContext $userContext,
  ) {
    $this->collaboratorRepository = $repositories->collaborator;
    $this->userContext = $userContext;
  }

  /**
   * @param string $id id of the collaborator to retrieve
   * @return Promise<CollaboratorEntity>
   */
  public function handle()
  {
    return $this->collaboratorRepository->findMany($this->userContext->getTenantId());
  }
}