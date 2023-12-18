<?php
/**
 * @Token
 *
 * Stores tokens and related data for an oauth provider
 */

namespace bchubbweb\Toucan;

use DateTime;

/**
 * Stores tokens and related data for an oauth provider
 */
class Token
{

    protected string $provider;

    protected string $access_token;

    protected string $refresh_token;

    protected string $last_refresh;

    protected int $expiry;

    /**
     * Instantiates all token data
     *
     * @param string $provider      - name of the oauth provider
     * @param string $access_token  - token used in basic requests
     * @param string $refresh_token - token used to refresh when access token expires
     * @param int    $last_refresh  - timestamp of the last refresh
     */
    public function __construct(string $provider, string $access_token, string $refresh_token, string $last_refresh, int $expiry)
    {
        $this->provider = $provider;

        $this->access_token = $access_token;

        $this->refresh_token = $refresh_token;

        $this->last_refresh = $last_refresh;

        $this->expiry = $expiry;
    }

    /**
     * Returns Access Token string
     *
     * @return string
     */
    public function accessToken(): string
    {
        return $this->access_token;

    }

    /**
     * Returns Refresh Token string
     *
     * @return string
     */
    public function refreshToken(): string
    {
        return $this->refresh_token;

    }

    /**
     * Returns the time of last refresh in DateTime format
     *
     * @return DateTime
     */
    public function lastRefresh(): DateTime
    {
        return new DateTime($this->last_refresh);

    }

    /**
     * Returns the token's provider
     *
     * @return string
     */
    public function provider(): string
    {
        return $this->provider;

    }

    public function hasExpired(): bool 
    {
        return ( $this->getExpiryLimit($this)->getTimestamp() > time() );
    }

    protected function getExpiryLimit(Token $token): DateTime
    {
        return new DateTime(date("y-m-d h:i:s", $token->lastRefresh()->getTimestamp() + $this->expiry));
    }



}
