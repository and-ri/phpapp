<?php

use SleekDB\Store;
use SleekDB\Query;

class Database {
    protected $database_dir = DIR_DATABASE;
    protected $configuration;
    
    public $tables;

    public function __construct() {
        $this->configuration = array(
            "auto_cache" => true,
            "cache_lifetime" => null,
            "timeout" => false,
            "primary_key" => "_id",
            "search" => [
                "min_length" => 2,
                "mode" => "or",
                "score_key" => "scoreKey",
                "algorithm" => Query::SEARCH_ALGORITHM["hits"]
            ],
            "folder_permissions" => 0755
        );

        $this->tables = new stdClass();
    
        $tables = include DIR_CONFIG . 'database.php';
    
        foreach ($tables as $table) {
            $this->tables->{$table} = new Store($table, $this->database_dir, $this->configuration);
        }
    }    
}