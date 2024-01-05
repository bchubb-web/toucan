<?php
/**
 * @Client
 *
 * communicates between application and OAuth provider
 *
 * Handles tokens, HTTP requests
 */

namespace bchubbweb\Toucan\Client;

use bchubbweb\Toucan\Token as Token;

use GuzzleHttp\Client as HttpClient;

use Psr\Http\Message\ResponseInterface;

/**
 * Communicates with the provider
 */
class Communicator implements CommunicatorInterface
{
    public function sendRefreshRequest(Token $token, string $refresh_endpoint, array $body, array $headers, HttpClient $http_client): ResponseInterface
    {
        $response = $http_client->request('POST', $refresh_endpoint, [

            "body" => $body,
            ...$headers,

        ]);

        return $response;
    }

    public function getRefreshBody(Token $token): array
    {
        $post_body = [
            "grant_type" => "refresh_token",
            "refresh_token" => $token->refreshToken(),
        ];

        return $post_body;
    }

    public function getRefreshHeaders(Token $token): array
    {
        $headers = [
            'auth'
        ];

        return $headers;
    }

    protected function formatAuthorizationHeader(string $base, ?array $other): string
    {
        return $base;
    }

}
