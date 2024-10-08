<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators;

use Vertuoza\Repositories\Interfaces\MutationDataInterface;

class CollaboratorMutationData implements MutationDataInterface
{
    public string $name;
    public string $firstName;
}