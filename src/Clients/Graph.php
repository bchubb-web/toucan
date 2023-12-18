<?php

namespace bchubbwebb\Toucan\Clients;

class Graph extends \bchubbweb\Toucan\Client
{

    protected array $scopes = ["offline_access", "user.read", "mail.read", "Directory.ReadWrite.All", "Team.Create", "Group.ReadWrite.All", "TeamMember.ReadWrite.All", "TeamMember.Read.All", "Files.ReadWrite", "Files.ReadWrite.All", "Sites.ReadWrite.All"];

    public function __construct()
    {
        
    }
}
