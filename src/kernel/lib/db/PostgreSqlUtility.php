<?php
/**
 * PostgreSQL Utility class
 */
namespace Lib\Db;

class PostgreSqlUtility extends \Lib\Db\PostgreSql {

    /**
     * Get Total Database size
     *
     * @throws \Exception
     */
    public function getDatabaseTotalSize() : array {
        return $this->fetchAssoc("SELECT pg_size_pretty(pg_database_size(current_database())) AS total_db_size;");
    }

    /**
     * @throws \Exception
     */
    public function getDatabaseTotalSizeBytes() : array {
        return $this->fetchAssoc("SELECT pg_database_size(current_database()) AS total_size_in_bytes;");
    }
}