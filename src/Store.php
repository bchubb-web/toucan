<?php

namespace bchubbwebb\Toucan;

use mysqli;

class Store
{

    protected \mysqli $conn;

    protected string $table_name;

    public function __construct( mysqli $database_connection, string $table_name="toucan_tokens" )
    {
        $this->conn = $database_connection;

        $this->table_name = $table_name;

        $this->verifyTable()?->createTable();

    }

    protected function verifyTable(): ?Store
    {
        return $this->conn->query("SELECT 1 FROM {$this->table_name} LIMIT 1") ? null : $this;
    }

    protected function createTable(): bool 
    {
        $stmt = $this->conn->query(
            "
            CREATE TABLE toucan_tokens (
                toucan_tokensId INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                provider VARCHAR(16) NOT NULL,
                access_token VARCHAR(4096) NOT NULL,
                refresh_token VARCHAR(2048) NOT NULL,
                last_refresh TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                meta longtext
            )
        "
        );

        return $stmt ? true : false;
    }

    public function retrieve( string $client ): ?Token
    {

        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE provider=?");

        $stmt->bind_param('s', $client);
        $stmt->execute();

        $result =  $stmt->get_result()?->fetch_assoc();
        
        return $result ? new Token($result["provider"], $result["access_token"], $result["refresh_token"], $result["last_refresh"]) : null;

    }

    public function update(Token $token): bool
    {

        $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET `access_token` = ?, `refresh_token` = ? WHERE `provider` = '{$token->provider}'");
        $stmt->bind_param('ss', $token->AccessToken(), $token->RefreshToken());
        return $stmt->execute();

    }

}
