<?php

namespace Vertuoza\Usecases\Collaborators;

use React\Promise\Promise;
use Vertuoza\Api\Graphql\Context\UserRequestContext;
use Vertuoza\Repositories\Collaborators\CollaboratorRepository;
use Vertuoza\Repositories\RepositoriesFactory;

class CollaboratorByIdUseCase
{
  private CollaboratorRepository $collaboratorRepository;
  private UserRequestContext $userContext;
  public function __construct(
    RepositoriesFactory $repositories,
    UserRequestContext $userContext
  ) {
    $this->collaboratorRepository = $repositories->collaborator;
    $this->userContext = $userContext;
  }

  /**
   * @param string $id id of the collaborator to retrieve
   * @param int $page page of the collaborator to retrieve
   * @return Promise<CollaboratorEntity>
   */
  public function handle(string $id): Promise
  {
    return $this->collaboratorRepository->getById($id, $this->userContext->getTenantId());
  }
}
