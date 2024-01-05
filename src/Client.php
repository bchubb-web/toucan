<?php
/**
 * @Client
 *
 * communicates between application and OAuth provider
 *
 * Handles tokens, HTTP requests
 *
 */

namespace bchubbweb\Toucan;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\StreamInterface;

/**
 * Basic template for OAuth client
 */
class Client implements ClientInterface
{
    protected string $provider;

    protected string $refresh_endpoint;

    protected string $authenticate_endpoint;

    protected string $authorize_endpoint;

    protected array $scopes;

    protected int $expiry;

    protected Token $token;


    public function __construct(string $provider, string $authenticate_endpoint, string $refresh_endpoint, array | string $scopes, int $expiry = 3600, Store $store)
    {
        $this->setProvider($provider);

        $this->authenticate_endpoint = $authenticate_endpoint;

        $this->refresh_endpoint = $refresh_endpoint;

        $this->setScopes($scopes);

        $this->expiry = $expiry;

        $this->token = $store->retrieve($this);

        $this->handleRefresh($this->getToken(), $store);
    }


    /**
     * Start the authentication flow, send user to the authentication page
     */
    public function authenticate(): ?Client
    {
        if (isset($_GET['code'])) {
            return $this;
        }

        // build query
        $url = $this->authenticationUrlQuery($this->authenticate_endpoint);

        // redirect to authorize
        header("Location: $url");
    }

    public function authorize()
    {
    }

    protected function authenticationUrlQuery(string $base_url): string
    {
        $url = rtrim($base_url, '/\\');

        $params = [
            'response_type' => 'code',
            'scope' => $this->getScopes(),
        ];

        return $url . '?' . http_build_query($params);
    }


    protected function handleRefresh(Token $token, Store $store)
    {
        if ($token->hasExpired()) {
            $new_token = $this->refreshAccessToken($token);
        }

        if (isset($new_token) && $token->lastRefresh() !== $new_token->lastRefresh()) {
            $store->update($new_token);
            $token = $new_token;
        }
    }

    protected function refreshAccessToken(Token $token): ?Token
    {
        $request_handler = new Client\Communicator();
        $http_client = new HttpClient();

        $headers = $request_handler->getRefreshHeaders($token);
        $body = $request_handler->getRefreshBody($token);

        $refresh_response = $request_handler->sendRefreshRequest($token, $this->refresh_endpoint, $body, $headers, $http_client)->getBody();

        return $this->parseForToken($token, $refresh_response);
    }

    protected function parseForToken(Token $current_token, StreamInterface $response_body): ?Token
    {
        $response_body_assoc = json_decode($response_body, true);

        if (!array_key_exists('access_token', $response_body_assoc)) {
            throw new \Exception("Access token not found in response body");
        }

        if (!array_key_exists('refresh_token', $response_body_assoc)) {
            throw new \Exception("Refresh token not found in response body");
        }

        return new Token($current_token->provider(), $response_body_assoc['access_token'], $response_body_assoc['refresh_token'], (string) time(), $this->expiry);
    }



    public function getToken(): Token
    {
        return $this->token;
    }

    protected function setToken(Token $new_token): void
    {
        $this->token = $new_token;
    }

    protected function getScopes($delimeter = " "): string
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

    protected function setExpiry(string $expiry): void
    {
        $this->expiry = $expiry;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    protected function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

}
