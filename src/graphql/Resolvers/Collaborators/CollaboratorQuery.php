<?php

namespace Vertuoza\Api\Graphql\Resolvers\Collaborators;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use Vertuoza\Api\Context\VertuozaContext;
use Vertuoza\Api\Graphql\Context\RequestContext;
use Vertuoza\Api\Graphql\Types;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;

class CollaboratorQuery
{
  static function get()
  {
    return [
      'collaboratorById' => [
        'type' => Types::get(Collaborator::class),
        'args' => [
          'id' => new NonNull(Types::string()),
        ],
        'resolve' => static function ($rootValue, $args, RequestContext $context) {
          try {
            return $context->useCases->collaborator
              ->collaboratorById
              ->handle($args['id'], $context);
          } catch (\Throwable $e) {
            ApplicationLogger::getInstance()->error($e, 'COLLABORATOR_BY_ID', new LogContext(null));
            throw $e;
          }
        }
      ],
      'collaborators' => [
        'type' => new NonNull(new ListOfType(Types::get(Collaborator::class))),
        'resolve' => static function ($rootValue, $args, RequestContext $context) {
          try {
            return $context->useCases->collaborator
              ->collaboratorsFindMany
              ->handle($context);
          } catch (\Throwable $e) {
            ApplicationLogger::getInstance()->error($e, 'COLLABORATORS', new LogContext(null));
            throw $e;
          }
        }
      ],
    ];
  }
}