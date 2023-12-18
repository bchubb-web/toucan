<?php
/**
 * @Client
 *
 * communicates between application and OAuth provider
 *
 * Handles tokens, HTTP requests
 */

namespace bchubbweb\Toucan\Client;


use \bchubbweb\Toucan\Token as Token;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\BadResponseException;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Communicates with the provider
 */
class Communicator implements CommunicatorInterface
{


    public function sendRefreshRequest(Token $token, string $refresh_endpoint, HttpClient $http_client): ResponseInterface
    {

        $response = $http_client->request('POST', $refresh_endpoint, [

            "body" => $this->createRefreshBody($token),
            ...$this->createRefreshHeaders($token),

        ]);

        return $response;

    }

    protected function formatAuthorizationHeader(string $base, ?array $other): string 
    {
        return $base;
    }

    protected function createRefreshBody(Token $token): array
    {
        $post_body = [
            "grant_type" => "refresh_token",
            "refresh_token" => $token->refreshToken(),
        ];

        return $post_body;
    }
    
    protected function createRefreshHeaders(Token $token): array 
    {
        $headers = [

        ];

        return $headers;
    }
}
