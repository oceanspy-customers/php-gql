<?php

declare(strict_types=1);

namespace Vertuoza\Api\Graphql\Context;

use Overblog\PromiseAdapter\PromiseAdapterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Vertuoza\Usecases\UseCasesFactory;
use Vertuoza\Repositories\Database\QueryBuilder;
use Vertuoza\Repositories\RepositoriesFactory;

class RequestContext
{
    public function __construct()
    {
    }

    public ServerRequestInterface $request;
    public UserRequestContext $userContext;
    public UseCasesFactory $useCases;
    public array $headerContext;

    function addCookie(string $cookieName, string $cookieValue, int $exp = 0, string $domain = "", string $path = "/", bool $secure = false, bool $httpOnly = false, string $sameSite = "Lax")
    {
        $expires = gmdate('D, d M Y H:i:s T', $exp); // Converting the expiration time to the correct format
        $path = "/";

        $cookieHeader = "$cookieName=$cookieValue; Expires=$expires; Path=$path; SameSite=$sameSite";
        if ($httpOnly) {
            $cookieHeader .= "; HttpOnly";
        }
        if ($secure) {
            $cookieHeader .= "; Secure";
        }
        if ($domain !== "") {
            $cookieHeader .= " Domain=$domain;";
        }

        $this->headerContext[] = ["Set-Cookie" => $cookieHeader];
    }

    function isLogged(): bool
    {
        return isset($this->userContext) && $this->userContext->isLogged();
    }

    static function middleware(PromiseAdapterInterface $dataLoaderPromiseAdapter)
    {
        return function (ServerRequestInterface $request, callable $next) use ($dataLoaderPromiseAdapter) {
            // Recreate a new connection each http call/

            $database = new QueryBuilder();

            $userContext = new UserRequestContext('448ef4f1-56e1-48be-838c-d147b5f09705', '112c33ae-3dbe-431b-994d-fffffe6fd49b');

            $repositories = new RepositoriesFactory($database, $dataLoaderPromiseAdapter);

            $useCases = new UseCasesFactory($userContext, $repositories);

            $context = new RequestContext();
            $context->useCases = $useCases;
            $context->request = $request;
            $context->headerContext = array();
            $context->userContext = $userContext;


            return $next(
                $request->withAttribute('app-context', $context)
            )->then(function (ResponseInterface $response) use ($database, $context) {

                foreach ($context->headerContext as $header) {
                    foreach ($header as $name => $value) {
                        $response = $response->withHeader($name, $value);
                    }
                }

                $database->getConnection()->disconnect();
                return $response;
            });
        };
    }
}

/**
 * Insert context in app-context attribute
 */
