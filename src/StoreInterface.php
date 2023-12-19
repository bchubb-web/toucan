<?php
/**
 * @Store
 *
 * database handler
 *
 * Handles updating and reading tokens from provided database
 */

namespace bchubbweb\Toucan;

use mysqli;

/**
 * Handles token read and write
 */
interface StoreInterface
{

    protected \mysqli $conn;

    protected string $table_name;

    /**
     * Instantiates db and handles table creation
     *
     * @param mysqli $database_connection - connection to desired database
     * @param string $table_name          - desired name for storage table
     */
    public function __construct( mysqli $database_connection, string $table_name="toucan_tokens" );


    /**
     * Gets the desired oauth token or null
     *
     * @param  Client $client - indicates the desired token
     *
     * @return ?Token
     */
    public function retrieve( Client $client ): ?Token;

    /**
     * Updates an oauth token and indicates the success
     *
     * @param Token $token - current state of the token
     *
     * @return bool
     */
    public function update(Token $token): bool;

}
