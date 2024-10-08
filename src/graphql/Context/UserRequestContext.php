<?php

namespace Vertuoza\Api\Graphql\Context;

class UserRequestContext
{
  private string $userId;
  private string $tenantId;

  public function __construct(string $userId, string $tenantId)
  {
    $this->userId = $userId;
    $this->tenantId = $tenantId;
  }

  public function getUserId(): string
  {
    return $this->userId;
  }

  public function getTenantId(): string
  {
    return $this->tenantId;
  }

  public function isLogged(): bool
  {
    return $this->userId !== null && $this->tenantId !== null;
  }
}
