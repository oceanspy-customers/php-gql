<?php

namespace Vertuoza\Api\Graphql\Resolvers;

use GraphQL\Type\Definition\ObjectType;
use Vertuoza\Api\Graphql\Resolvers\Collaborators\CollaboratorQuery;
use Vertuoza\Api\Graphql\Resolvers\Settings\UnitTypes\UnitTypeQuery;
use Vertuoza\Api\Graphql\Types;

final class Query extends ObjectType
{
  public function __construct()
  {
    $config = [
      'fields' => function () {
        return [
          'hello' => [
            'type' => Types::string(),
            'resolve' => function ($root, $args) {
              return 'world';
            }
          ],
          ...UnitTypeQuery::get(),
          ...CollaboratorQuery::get()
        ];
      }
    ];
    parent::__construct($config);
  }
}
