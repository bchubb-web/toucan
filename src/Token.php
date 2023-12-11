<?php

namespace bchubbwebb\Toucan;

use DateTime;

class Token
{

    public string $provider;

    protected string $access_token;

    protected string $refresh_token;

    protected string $last_refresh;

    public function __construct(string $provider, string $access_token, string $refresh_token, string $last_refresh)
    {
        $this->provider = $provider;

        $this->access_token = $access_token;

        $this->refresh_token = $refresh_token;

        $this->last_refresh = $last_refresh;
    }

    public function AccessToken(): string
    {
        return $this->access_token;

    }

    public function RefreshToken(): string
    {
        return $this->refresh_token;

    }

    public function lastRefresh(): DateTime
    {
        return new DateTime($this->last_refresh);

    }

}
