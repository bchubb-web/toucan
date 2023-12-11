<?php

namespace bchubbwebb\Toucan;

use DateTime;
use GuzzleHttp\Client as HttpClient;
 

class Client
{

    protected string $provider;   

    protected string $refresh_endpoint;

    protected string $authenticate_endpoint;

    protected array $scopes;

    protected int $expiry;

    protected Token $token;

    public function __construct( string $provider, string $authenticate_endpoint, string $refresh_endpoint, array $scopes, int $expiry=3600, Store $store)
    {
        $this->provider = $provider;

        $this->authenticate_endpoint = $authenticate_endpoint;

        $this->refresh_endpoint = $refresh_endpoint;

        $this->scopes = $scopes;

        $this->expiry = $expiry;

        $this->token = $store->retrieve($this->provider);
        
        $previous_last_refresh = $this->token->lastRefresh();

        $this->expired()?->refresh();

        if ($previous_last_refresh !== $this->token->lastRefresh()) {
            $store->update($this->token);
        }

    }

    protected function authenticate() 
    {

    }

    protected function refresh()
    {
        
    }

    public function token(): Token
    {
        return $this->token;
        
    }

    protected function expired(): ?Client
    {
        return ( $this->getExpiryLimit($this->token)->getTimestamp() > time() ) ? $this : null;
    }

    protected function getExpiryLimit(Token $token): DateTime
    {
        return new DateTime(date("y-m-d h:i:s", $token->lastRefresh()->getTimestamp() + $this->expiry));
    }

    protected function getScopes($delimeter=" "): string
    {
        return implode($delimeter, $this->scopes);

    }

}
