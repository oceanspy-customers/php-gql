<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/libs/Logger/index.php';

use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Middleware\StreamingRequestMiddleware;
use Vertuoza\Libs\Logger\ApplicationLogger;
use Vertuoza\Libs\Logger\LogContext;
use function React\Promise\resolve;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;
use function Vertuoza\Libs\Logger\startLogger;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter;
use Vertuoza\Api\Graphql\Context\RequestContext;
use Vertuoza\Api\Graphql\GqlMiddlewares;


$logger = startLogger();

ini_set('memory_limit', $_ENV['MEMORY_LIMIT']);

try {
    $graphQLPromiseAdapter = new ReactPromiseAdapter();
    $dataLoaderPromiseAdapter = new \Overblog\PromiseAdapter\Adapter\ReactPromiseAdapter();

    $http = new React\Http\HttpServer(
        new StreamingRequestMiddleware(),
        new RequestBodyBufferMiddleware(20 * 1024 * 1024),
        new RequestBodyParserMiddleware(20 * 1024 * 1024, 1),
        GqlMiddlewares::sandbox(),
        RequestContext::middleware($dataLoaderPromiseAdapter),
        GqlMiddlewares::schema($graphQLPromiseAdapter, $dataLoaderPromiseAdapter),
        fn (ServerRequest $request) => resolve(new Response(404))
    );

    $socket = new React\Socket\SocketServer('0.0.0.0:' . $_ENV['PORT']);
    $http->listen($socket);

    ApplicationLogger::getInstance()->info("Server running on port " . $_ENV['PORT'] . PHP_EOL, new LogContext(null));
} catch (Throwable $e) {
    ApplicationLogger::getInstance()->error($e, 'UNKNOWN_ERROR', new LogContext(null));
    exit(0);
}
