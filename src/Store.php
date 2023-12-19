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
class Store implements StoreInterface
{

    protected \mysqli $conn;

    protected string $table_name;

    /**
     * Instantiates db and handles table creation
     *
     * @param mysqli $database_connection - connection to desired database
     * @param string $table_name          - desired name for storage table
     */
    public function __construct( mysqli $database_connection, string $table_name="toucan_tokens" )
    {
        $this->conn = $database_connection;

        $this->table_name = $table_name;

        $this->verifyTable($this->table_name)?->createTable($this->table_name);

    }

    /**
     * Determines existance of storage table
     *
     * @return ?Store
     */
    protected function verifyTable(string $table_name): ?Store
    {
        return $this->conn->query("SELECT 1 FROM `{$table_name}` LIMIT 1") ? null : $this;
    }

    /**
     * Creates the table with desired cols
     *
     * @return bool
     */
    protected function createTable($table_name): bool 
    {
        $result = $this->conn->query(
            "
            CREATE TABLE `{$table_name}` (
                `toucan_tokensId` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `provider` VARCHAR(16) NOT NULL,
                `access_token` VARCHAR(4096) NOT NULL,
                `refresh_token` VARCHAR(2048) NOT NULL,
                `last_refresh` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                `meta` longtext
            )
        "
        );

        return (bool) $result;
    }

    /**
     * Gets the desired oauth token or null
     *
     * @param  Client $client - indicates the desired token
     *
     * @return ?Token
     */
    public function retrieve( Client $client ): ?Token
    {

        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE `provider`=?");

        $stmt->bind_param('s', $client->getProvider());
        $stmt->execute();

        $result =  $stmt->get_result()?->fetch_assoc();
        
        return $result ? new Token($result["provider"], $result["access_token"], $result["refresh_token"], $result["last_refresh"], $client->getExpiry()) : null;

    }

    /**
     * Updates an oauth token and indicates the success
     *
     * @param Token $token - current state of the token
     *
     * @return bool
     */
    public function update(Token $token): bool
    {

        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET `access_token` = ?, `refresh_token` = ? WHERE `provider` = '{$token->provider()}'");
        $stmt->bind_param('ss', $token->accessToken(), $token->refreshToken());
        return $stmt->execute();

    }

}
