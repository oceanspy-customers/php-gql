<?php

namespace Vertuoza\Api\Graphql\Resolvers\Settings\UnitTypes;

use Vertuoza\Api\Graphql\Types;
use GraphQL\Type\Definition\InputObjectType;

class UnitTypeCreateInput extends InputObjectType
{
  public function __construct()
  {
    parent::__construct([
      'name' => 'UnitTypeCreateInput',
      'description' => 'Unit type',
      'fields' => static fn (): array => [
        'name' => [
          'description' => "Name of the unit type",
          'type' => Types::string()
        ],
      ],
    ]);
  }
}