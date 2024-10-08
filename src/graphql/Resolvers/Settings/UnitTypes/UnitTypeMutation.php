<?php

namespace Vertuoza\Api\Graphql\Resolvers\Settings\UnitTypes;

use Vertuoza\Api\Graphql\Context\RequestContext;
use Vertuoza\Api\Graphql\Types;
use GraphQL\Type\Definition\NonNull;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;

class UnitTypeMutation
{
  static function get(): array
  {
    return [
      'unitTypeCreate' => [
        'type' => Types::get(UnitType::class),
        'args' => [
          'input' => new NonNull(Types::get(UnitTypeCreateInput::class)),
        ],
        'resolve' => static function ($rootValue, $args, RequestContext $context) {
          try {
            $newId = $context->useCases->unitType
              ->unitTypeCreate
              ->handle($args['input']['name']);
            return ['id' => $newId];
          } catch (\Throwable $e) {
            ApplicationLogger::getInstance()->error($e, 'UNIT_TYPE_CREATE', new LogContext(null));
            throw $e;
          }
        }
      ],
    ];
  }
}