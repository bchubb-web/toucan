<?php
/**
 * @Client
 *
 * communicates between application and OAuth provider
 *
 * Handles tokens, HTTP requests
 */

namespace bchubbweb\Toucan;

/**
 * Basic template for OAuth client
 */
interface ClientInterface
{
    public function __construct(string $provider, string $authenticate_endpoint, string $refresh_endpoint, array | string $scopes, int $expiry = 3600, Store $store);


    public function getToken(): Token;

}
