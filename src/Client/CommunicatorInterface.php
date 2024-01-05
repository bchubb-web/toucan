<?php
/**
 * @ClientInterface
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
 * Implements medthods to communicate with a provider
 */
interface CommunicatorInterface
{
    public function sendRefreshRequest(Token $token, string $refresh_endpoint, array $body, array $headers, HttpClient $http_client): ResponseInterface;

    public function getRefreshHeaders(Token $token): array;

    public function getRefreshBody(Token $token): array;

}
