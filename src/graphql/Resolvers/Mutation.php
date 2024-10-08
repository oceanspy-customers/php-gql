<?php

namespace Vertuoza\Api\Graphql\Resolvers;

use GraphQL\Type\Definition\ObjectType;
use Vertuoza\Api\Graphql\Resolvers\Settings\UnitTypes\UnitTypeMutation;

final class Mutation extends ObjectType
{
  public function __construct()
  {
    $config = [
      'fields' => function () {
        return [
          ...UnitTypeMutation::get()
        ];
      }
    ];
    parent::__construct($config);
  }
}