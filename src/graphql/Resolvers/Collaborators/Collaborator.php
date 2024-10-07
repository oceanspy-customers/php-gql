<?php

namespace Vertuoza\Api\Graphql\Resolvers\Collaborators;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use Vertuoza\Api\Graphql\Types;

class Collaborator extends ObjectType
{
  public function __construct()
  {
    parent::__construct([
      'name' => 'Collaborator',
      'description' => 'Collaborator',
      'fields' => static fn (): array => [
        'id' => [
          'description' => "Unique identifier of the collaborator",
          'type' => Types::id(),
        ],
        'name' => [
          'description' => "Name of the collaborator",
          'type' => Types::string()
        ],
        'firstName' => [
          'description' => "First Name of the collaborator",
          'type' => Types::string()
        ],
        'isSystem' => [
          'description' => "To know if the collaborator has been created by the user or is a system collaborator of Vertuoza",
          'type' => new NonNull(Types::boolean())
        ],
      ],
    ]);
  }
}