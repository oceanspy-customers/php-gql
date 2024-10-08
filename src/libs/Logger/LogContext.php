<?php

namespace Vertuoza\Libs\Logger;

use Vertuoza\Api\Graphql\Context\UserRequestContext;

class LogContext
{
  function __construct(private ?UserRequestContext $userContext)
  {
  }

  public function getTenantId(): string
  {
    return $this->userContext?->getTenantId() ?? '';
  }

  public function getUserId(): string
  {
    return $this->userContext?->getUserId() ?? '';
  }
}
