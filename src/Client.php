<?php
/**
 * @Client
 *
 * communicates between application and OAuth provider
 *
 * Handles tokens, HTTP requests
 */

namespace bchubbweb\Toucan;

use GuzzleHttp\Client as HttpClient;
use PhpParser\Comment;

/**
 * Basic template for OAuth client
 */
class Client implements ClientInterface
{

    protected string $provider;   

    protected string $refresh_endpoint;

    protected string $authenticate_endpoint;

    protected array $scopes;

    protected int $expiry;

    protected Token $token;


    public function __construct( string $provider, string $authenticate_endpoint, string $refresh_endpoint, array | string $scopes, int $expiry=3600, Store $store)
    {
        $this->provider = $provider;

        $this->authenticate_endpoint = $authenticate_endpoint;

        $this->refresh_endpoint = $refresh_endpoint;

        $this->setScopes($scopes);

        $this->expiry = $expiry;

        $this->token = $store->retrieve($this->provider);
        

        $this->handleRefresh($this->getToken(), $store);

    }


    protected function authenticate() 
    {

    }

    protected function handleRefresh(Token $token, Store $store)
    {
        if ($token->hasExpired()) $new_token = $this->refreshAccessToken($token);

        if (isset($new_token) && $token->lastRefresh() !== $new_token->lastRefresh()) {
            $store->update($new_token);
            $token = $new_token;
        }
    }

    protected function refreshAccessToken(Token $token): ?Token
    {
        $request_handler = new Client\Communicator();

        $result = $request_handler->sendRefreshRequest($token, $this->refresh_endpoint);

        return new Token(...[]);

    }


    public function getToken(): Token
    {
        return $this->token;
    }

    protected function setToken(Token $new_token): void 
    {
        $this->token = $new_token;
    }

    protected function getScopes($delimeter=" "): string
    {
        return implode($delimeter, $this->scopes);
    }

    protected function setScopes(array | string $scopes, string $delimeter = ' '): void
    {
        if (gettype($scopes) == "string") {
            $scopes = explode($delimeter, $scopes);
        }

        $this->scopes = $scopes;
    }

}
