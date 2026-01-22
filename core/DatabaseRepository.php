<?php
/**
 * ============================================================================
 *  HytaBansWeb
 * ============================================================================
 *
 *  Plugin Name:   HytaBansWeb
 *  Description:   A modern, secure, and responsive web interface for HytaBans punishment management system.
 *  Version:       1.0
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

class DatabaseRepository
{
    private PDO $connection;
    private string $tablePrefix;
    
    public function __construct(PDO $connection, string $tablePrefix = 'hb_')
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
    }
    
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }
    
    /**
     * Get bans from hb_bans table
     * Structure: id, player_uuid, player_name, reason, issued_by, issued_by_name, created_at, expires_at, permanent, removed, removed_by, removed_by_name, removed_at
     */
    public function getBans(int $limit = 20, int $offset = 0, bool $activeOnly = true, string $sort = 'created_at', string $order = 'DESC', bool $showSilent = true): array
    {
        try {
            $table = $this->tablePrefix . 'bans';
            
            // Validate sort and order parameters
            $allowedSorts = ['id', 'player_name', 'reason', 'issued_by_name', 'created_at', 'expires_at', 'removed'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
            
            // Map old sort names to new column names
            $sortMapping = [
                'name' => 'player_name',
                'banned_by_name' => 'issued_by_name',
                'time' => 'created_at',
                'until' => 'expires_at',
                'active' => 'removed'
            ];
            $sort = $sortMapping[$sort] ?? $sort;
            
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Detect if timestamps are in milliseconds
            $isMilliseconds = $this->detectMillisecondTimestamps($table);
            $currentTime = $isMilliseconds ? (time() * 1000) : time();
            
            // Build WHERE clause
            // Active = not removed AND (permanent OR expires_at = 0/-1 OR expires_at > now)
            if ($activeOnly) {
                $where = "WHERE removed = 0 AND (permanent = 1 OR expires_at IS NULL OR expires_at = 0 OR expires_at = -1 OR expires_at > :current_time)";
            } else {
                $where = "WHERE 1=1";
            }
            
            $sql = "SELECT id, player_uuid as uuid, player_name, reason, 
                           issued_by, issued_by_name as banned_by_name, 
                           created_at as time, expires_at as until,
                           permanent, removed,
                           removed_by, removed_by_name, removed_at,
                           CASE 
                               WHEN removed = 1 THEN 0
                               WHEN permanent = 1 THEN 1
                               WHEN expires_at IS NULL THEN 1
                               WHEN expires_at = 0 THEN 1
                               WHEN expires_at = -1 THEN 1
                               WHEN expires_at > :current_time2 THEN 1
                               ELSE 0
                           END as active
                    FROM {$table}
                    {$where}
                    ORDER BY {$sort} {$order}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            if ($activeOnly) {
                $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
            }
            $stmt->bindValue(':current_time2', $currentTime, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getBans: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get mutes from hb_mutes table
     */
    public function getMutes(int $limit = 20, int $offset = 0, bool $activeOnly = true, string $sort = 'created_at', string $order = 'DESC', bool $showSilent = true): array
    {
        try {
            $table = $this->tablePrefix . 'mutes';
            
            $allowedSorts = ['id', 'player_name', 'reason', 'issued_by_name', 'created_at', 'expires_at', 'removed'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
            
            $sortMapping = [
                'name' => 'player_name',
                'banned_by_name' => 'issued_by_name',
                'time' => 'created_at',
                'until' => 'expires_at',
                'active' => 'removed'
            ];
            $sort = $sortMapping[$sort] ?? $sort;
            
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Detect if timestamps are in milliseconds
            $isMilliseconds = $this->detectMillisecondTimestamps($table);
            $currentTime = $isMilliseconds ? (time() * 1000) : time();
            
            if ($activeOnly) {
                $where = "WHERE removed = 0 AND (permanent = 1 OR expires_at IS NULL OR expires_at = 0 OR expires_at = -1 OR expires_at > :current_time)";
            } else {
                $where = "WHERE 1=1";
            }
            
            $sql = "SELECT id, player_uuid as uuid, player_name, reason,
                           issued_by, issued_by_name as banned_by_name,
                           created_at as time, expires_at as until,
                           permanent, removed,
                           removed_by, removed_by_name, removed_at,
                           CASE 
                               WHEN removed = 1 THEN 0
                               WHEN permanent = 1 THEN 1
                               WHEN expires_at IS NULL THEN 1
                               WHEN expires_at = 0 THEN 1
                               WHEN expires_at = -1 THEN 1
                               WHEN expires_at > :current_time2 THEN 1
                               ELSE 0
                           END as active
                    FROM {$table}
                    {$where}
                    ORDER BY {$sort} {$order}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            if ($activeOnly) {
                $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
            }
            $stmt->bindValue(':current_time2', $currentTime, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getMutes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get warnings from hb_warnings table
     */
    public function getWarnings(int $limit = 20, int $offset = 0, string $sort = 'created_at', string $order = 'DESC'): array
    {
        try {
            $table = $this->tablePrefix . 'warnings';
            
            $allowedSorts = ['id', 'player_name', 'reason', 'issued_by_name', 'created_at', 'expires_at', 'removed'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
            
            $sortMapping = [
                'name' => 'player_name',
                'banned_by_name' => 'issued_by_name',
                'time' => 'created_at',
                'until' => 'expires_at',
                'active' => 'removed'
            ];
            $sort = $sortMapping[$sort] ?? $sort;
            
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            $currentTime = time();
            
            $sql = "SELECT id, player_uuid as uuid, player_name, reason,
                           issued_by, issued_by_name as banned_by_name,
                           created_at as time, expires_at as until,
                           permanent, removed,
                           removed_by, removed_by_name, removed_at,
                           CASE 
                               WHEN removed = 1 THEN 0
                               WHEN permanent = 1 THEN 1
                               WHEN expires_at IS NULL THEN 1
                               WHEN expires_at > :current_time THEN 1
                               ELSE 0
                           END as active
                    FROM {$table}
                    ORDER BY {$sort} {$order}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getWarnings: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get kicks from hb_kicks table
     */
    public function getKicks(int $limit = 20, int $offset = 0, string $sort = 'created_at', string $order = 'DESC'): array
    {
        try {
            $table = $this->tablePrefix . 'kicks';
            
            $allowedSorts = ['id', 'player_name', 'reason', 'issued_by_name', 'created_at'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
            
            $sortMapping = [
                'name' => 'player_name',
                'banned_by_name' => 'issued_by_name',
                'time' => 'created_at'
            ];
            $sort = $sortMapping[$sort] ?? $sort;
            
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            $sql = "SELECT id, player_uuid as uuid, player_name, reason,
                           issued_by, issued_by_name as banned_by_name,
                           created_at as time,
                           0 as active
                    FROM {$table}
                    ORDER BY {$sort} {$order}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getKicks: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get player punishments by UUID or name
     */
    public function getPlayerPunishments(string $identifier): array
    {
        try {
            $identifier = trim($identifier);
            if (empty($identifier)) {
                return [];
            }
            
            $isUuid = SecurityManager::validateUuid($identifier);
            
            if ($isUuid) {
                $field = 'player_uuid';
                $value = $identifier;
            } else {
                if (!SecurityManager::validateUsername($identifier)) {
                    return [];
                }
                $field = 'player_name';
                $value = $identifier;
            }
            
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            $currentTime = time();
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                
                if ($table === 'kicks') {
                    $sql = "SELECT '{$table}' as type, id, player_uuid as uuid, player_name,
                                   reason, issued_by_name as banned_by_name, created_at as time,
                                   0 as active
                            FROM {$fullTable} 
                            WHERE LOWER({$field}) = LOWER(:identifier)
                            ORDER BY created_at DESC";
                } else {
                    $sql = "SELECT '{$table}' as type, id, player_uuid as uuid, player_name,
                                   reason, issued_by_name as banned_by_name, created_at as time,
                                   expires_at as until, permanent, removed,
                                   CASE 
                                       WHEN removed = 1 THEN 0
                                       WHEN permanent = 1 THEN 1
                                       WHEN expires_at IS NULL THEN 1
                                       WHEN expires_at > :current_time THEN 1
                                       ELSE 0
                                   END as active
                            FROM {$fullTable}
                            WHERE LOWER({$field}) = LOWER(:identifier)
                            ORDER BY created_at DESC";
                }
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':identifier', $value, PDO::PARAM_STR);
                if ($table !== 'kicks') {
                    $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
                }
                $stmt->execute();
                
                $tableResults = $stmt->fetchAll();
                $results = array_merge($results, $tableResults);
            }
            
            usort($results, function($a, $b) {
                return $b['time'] <=> $a['time'];
            });
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error in getPlayerPunishments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get player name by UUID (returns from any table)
     */
    public function getPlayerName(string $uuid): ?string
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                $stmt = $this->connection->prepare(
                    "SELECT player_name FROM {$fullTable} WHERE player_uuid = :uuid ORDER BY created_at DESC LIMIT 1"
                );
                $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch();
                
                if ($result && !empty($result['player_name'])) {
                    return $result['player_name'];
                }
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error getting player name: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get punishment by ID and type
     */
    public function getPunishmentById(string $type, int $id): ?array
    {
        try {
            $allowedTypes = ['bans', 'mutes', 'warnings', 'kicks'];
            if (!in_array($type, $allowedTypes)) {
                return null;
            }
            
            $table = $this->tablePrefix . $type;
            
            // Detect if timestamps are in milliseconds
            $isMilliseconds = $this->detectMillisecondTimestamps($table);
            $currentTime = $isMilliseconds ? (time() * 1000) : time();
            
            if ($type === 'kicks') {
                $sql = "SELECT id, player_uuid as uuid, player_name, reason,
                               issued_by, issued_by_name as banned_by_name,
                               created_at as time,
                               0 as active, 0 as removed
                        FROM {$table}
                        WHERE id = :id";
            } else {
                $sql = "SELECT id, player_uuid as uuid, player_name, reason,
                               issued_by, issued_by_name as banned_by_name,
                               created_at as time, expires_at as until,
                               permanent, removed,
                               removed_by, removed_by_name, removed_at,
                               CASE 
                                   WHEN removed = 1 THEN 0
                                   WHEN permanent = 1 THEN 1
                                   WHEN expires_at IS NULL THEN 1
                                   WHEN expires_at = 0 THEN 1
                                   WHEN expires_at = -1 THEN 1
                                   WHEN expires_at > :current_time THEN 1
                                   ELSE 0
                               END as active
                        FROM {$table}
                        WHERE id = :id";
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if ($type !== 'kicks') {
                $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Error getting punishment by ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get statistics
     * Auto-detects if timestamps are in seconds or milliseconds
     */
    public function getStats(): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $stats = [];
        $currentTime = time();
        $currentTimeMs = $currentTime * 1000;
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$fullTable}");
                $result = $stmt->fetch();
                $stats[$table] = (int)($result['total'] ?? 0);
                
                if (in_array($table, ['bans', 'mutes'])) {
                    // Check if timestamps are in milliseconds by looking at a sample
                    $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                    $compareTime = $isMilliseconds ? $currentTimeMs : $currentTime;
                    
                    $stmt = $this->connection->prepare(
                        "SELECT COUNT(*) as active FROM {$fullTable} 
                         WHERE removed = 0 AND (permanent = 1 OR expires_at IS NULL OR expires_at = 0 OR expires_at = -1 OR expires_at > :current_time)"
                    );
                    $stmt->bindValue(':current_time', $compareTime, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch();
                    $stats[$table . '_active'] = (int)($result['active'] ?? 0);
                }
            } catch (PDOException $e) {
                error_log("Error getting stats for {$table}: " . $e->getMessage());
                $stats[$table] = 0;
                if (in_array($table, ['bans', 'mutes'])) {
                    $stats[$table . '_active'] = 0;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Get recent activity statistics (last 24h, 7d, 30d)
     */
    public function getRecentActivity(): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $periods = [
            'last_24h' => 86400,      // 24 hours in seconds
            'last_7d' => 604800,      // 7 days
            'last_30d' => 2592000     // 30 days
        ];
        
        $activity = [];
        $currentTime = time();
        
        foreach ($periods as $periodName => $seconds) {
            $activity[$periodName] = [];
            $startTime = $currentTime - $seconds;
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                
                try {
                    // Check if timestamps are in milliseconds
                    $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                    $compareStartTime = $isMilliseconds ? ($startTime * 1000) : $startTime;
                    
                    $stmt = $this->connection->prepare(
                        "SELECT COUNT(*) as count FROM {$fullTable} WHERE created_at >= :start_time"
                    );
                    $stmt->bindValue(':start_time', $compareStartTime, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch();
                    $activity[$periodName][$table] = (int)($result['count'] ?? 0);
                } catch (PDOException $e) {
                    error_log("Error getting recent activity for {$table}: " . $e->getMessage());
                    $activity[$periodName][$table] = 0;
                }
            }
        }
        
        return $activity;
    }
    
    /**
     * Get most active staff members
     */
    public function getMostActiveStaff(int $limit = 10): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $staffStats = [];
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $stmt = $this->connection->query(
                    "SELECT staff_name, COUNT(*) as count 
                     FROM {$fullTable} 
                     WHERE staff_name IS NOT NULL AND staff_name != '' AND staff_name != 'Console'
                     GROUP BY staff_name"
                );
                
                while ($row = $stmt->fetch()) {
                    $staffName = $row['staff_name'];
                    if (!isset($staffStats[$staffName])) {
                        $staffStats[$staffName] = [
                            'staff_name' => $staffName,
                            'bans' => 0,
                            'mutes' => 0,
                            'warnings' => 0,
                            'kicks' => 0,
                            'total_punishments' => 0
                        ];
                    }
                    $staffStats[$staffName][$table] = (int)$row['count'];
                    $staffStats[$staffName]['total_punishments'] += (int)$row['count'];
                }
            } catch (PDOException $e) {
                error_log("Error getting staff stats for {$table}: " . $e->getMessage());
            }
        }
        
        // Sort by total punishments descending
        usort($staffStats, function($a, $b) {
            return $b['total_punishments'] - $a['total_punishments'];
        });
        
        return array_slice($staffStats, 0, $limit);
    }
    
    /**
     * Get top ban reasons
     */
    public function getTopBanReasons(int $limit = 10): array
    {
        $table = $this->tablePrefix . 'bans';
        
        try {
            $stmt = $this->connection->query(
                "SELECT reason, COUNT(*) as count 
                 FROM {$table} 
                 WHERE reason IS NOT NULL AND reason != '' 
                 GROUP BY reason 
                 ORDER BY count DESC 
                 LIMIT {$limit}"
            );
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top ban reasons: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get daily activity for the last 7 days
     */
    public function getDailyActivity(): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $days = [];
        $currentTime = time();
        
        // Initialize days array for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $dayTimestamp = strtotime("-{$i} days", $currentTime);
            $dayName = date('l', $dayTimestamp);
            $dayDate = date('Y-m-d', $dayTimestamp);
            $days[$dayDate] = [
                'day_name' => $dayName,
                'date' => $dayDate,
                'count' => 0
            ];
        }
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                // Check if timestamps are in milliseconds
                $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                $startTime = strtotime('-7 days', $currentTime);
                $compareStartTime = $isMilliseconds ? ($startTime * 1000) : $startTime;
                
                $stmt = $this->connection->prepare(
                    "SELECT created_at FROM {$fullTable} WHERE created_at >= :start_time"
                );
                $stmt->bindValue(':start_time', $compareStartTime, PDO::PARAM_INT);
                $stmt->execute();
                
                while ($row = $stmt->fetch()) {
                    $timestamp = (int)$row['created_at'];
                    // Convert from milliseconds if needed
                    if ($isMilliseconds) {
                        $timestamp = (int)($timestamp / 1000);
                    }
                    $dayDate = date('Y-m-d', $timestamp);
                    if (isset($days[$dayDate])) {
                        $days[$dayDate]['count']++;
                    }
                }
            } catch (PDOException $e) {
                error_log("Error getting daily activity for {$table}: " . $e->getMessage());
            }
        }
        
        return array_values($days);
    }
    
    /**
     * Get most punished players
     */
    public function getMostPunishedPlayers(int $limit = 10): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $playerStats = [];
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $stmt = $this->connection->query(
                    "SELECT player_name, player_uuid, COUNT(*) as count 
                     FROM {$fullTable} 
                     WHERE player_name IS NOT NULL AND player_name != ''
                     GROUP BY player_uuid"
                );
                
                while ($row = $stmt->fetch()) {
                    $uuid = $row['player_uuid'] ?? $row['player_name'];
                    if (!isset($playerStats[$uuid])) {
                        $playerStats[$uuid] = [
                            'player_name' => $row['player_name'],
                            'player_uuid' => $row['player_uuid'] ?? null,
                            'bans' => 0,
                            'mutes' => 0,
                            'warnings' => 0,
                            'kicks' => 0,
                            'total_punishments' => 0
                        ];
                    }
                    $playerStats[$uuid][$table] = (int)$row['count'];
                    $playerStats[$uuid]['total_punishments'] += (int)$row['count'];
                }
            } catch (PDOException $e) {
                error_log("Error getting player stats for {$table}: " . $e->getMessage());
            }
        }
        
        // Sort by total punishments descending
        usort($playerStats, function($a, $b) {
            return $b['total_punishments'] - $a['total_punishments'];
        });
        
        return array_slice($playerStats, 0, $limit);
    }
    
    /**
     * Get total bans count
     */
    public function getTotalBans(bool $activeOnly = true): int
    {
        try {
            $table = $this->tablePrefix . 'bans';
            $currentTime = time();
            
            if ($activeOnly) {
                $stmt = $this->connection->prepare(
                    "SELECT COUNT(*) as total FROM {$table} 
                     WHERE removed = 0 AND (permanent = 1 OR expires_at IS NULL OR expires_at > :current_time)"
                );
                $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$table}");
            }
            
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total bans: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total mutes count
     */
    public function getTotalMutes(bool $activeOnly = true): int
    {
        try {
            $table = $this->tablePrefix . 'mutes';
            $currentTime = time();
            
            if ($activeOnly) {
                $stmt = $this->connection->prepare(
                    "SELECT COUNT(*) as total FROM {$table} 
                     WHERE removed = 0 AND (permanent = 1 OR expires_at IS NULL OR expires_at > :current_time)"
                );
                $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$table}");
            }
            
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total mutes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total warnings count
     */
    public function getTotalWarnings(): int
    {
        try {
            $table = $this->tablePrefix . 'warnings';
            $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$table}");
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total warnings: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total kicks count
     */
    public function getTotalKicks(): int
    {
        try {
            $table = $this->tablePrefix . 'kicks';
            $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$table}");
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total kicks: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Search players
     */
    public function searchPlayers(string $query, int $limit = 10): array
    {
        try {
            $query = trim($query);
            if (empty($query) || strlen($query) < 2) {
                return [];
            }
            
            $results = [];
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                
                $sql = "SELECT DISTINCT player_uuid as uuid, player_name as name
                        FROM {$fullTable}
                        WHERE player_name LIKE :query OR player_uuid LIKE :query
                        LIMIT :limit";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                
                $tableResults = $stmt->fetchAll();
                foreach ($tableResults as $row) {
                    $key = $row['uuid'];
                    if (!isset($results[$key])) {
                        $results[$key] = $row;
                    }
                }
            }
            
            return array_slice(array_values($results), 0, $limit);
        } catch (PDOException $e) {
            error_log("Error searching players: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top banned players
     */
    public function getTopBannedPlayers(int $limit = 10): array
    {
        try {
            $table = $this->tablePrefix . 'bans';
            
            $sql = "SELECT player_uuid as uuid, player_name as name, 
                           COUNT(*) as ban_count,
                           MAX(created_at) as last_ban_time
                    FROM {$table}
                    GROUP BY player_uuid, player_name
                    ORDER BY ban_count DESC, last_ban_time DESC
                    LIMIT :limit";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top banned players: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Get logs from hb_logs table
     */
    public function getLogs(int $limit = 50, int $offset = 0): array
    {
        try {
            $table = $this->tablePrefix . 'logs';
            
            $sql = "SELECT id, action, target_uuid, target_name, 
                           issued_by, issued_by_name, reason, duration, created_at
                    FROM {$table}
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Detect if timestamps in table are in milliseconds
     * Timestamps > 10000000000 are considered milliseconds (after year 2286 in seconds)
     */
    private function detectMillisecondTimestamps(string $table): bool
    {
        try {
            $stmt = $this->connection->query(
                "SELECT created_at FROM {$table} WHERE created_at > 0 LIMIT 1"
            );
            $result = $stmt->fetch();
            
            if ($result && isset($result['created_at'])) {
                // If timestamp is larger than 10 billion, it's in milliseconds
                return (int)$result['created_at'] > 10000000000;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get current time for comparison based on table's timestamp format
     */
    public function getCurrentTimeForTable(string $table): int
    {
        $fullTable = $this->tablePrefix . $table;
        $isMs = $this->detectMillisecondTimestamps($fullTable);
        return $isMs ? (time() * 1000) : time();
    }
    
    /**
     * Get player by UUID or name
     */
    public function getPlayerByIdentifier(string $identifier): ?array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                // Try UUID first
                $stmt = $this->connection->prepare(
                    "SELECT player_uuid, player_name FROM {$fullTable} 
                     WHERE player_uuid = :identifier OR player_name = :identifier2
                     ORDER BY created_at DESC LIMIT 1"
                );
                $stmt->bindValue(':identifier', $identifier);
                $stmt->bindValue(':identifier2', $identifier);
                $stmt->execute();
                
                $result = $stmt->fetch();
                if ($result) {
                    return $result;
                }
            } catch (PDOException $e) {
                error_log("Error finding player in {$table}: " . $e->getMessage());
            }
        }
        
        return null;
    }
    
    /**
     * Get all punishments for a player by UUID (for Player History page)
     */
    public function getPlayerPunishmentsByUuid(string $playerUuid): array
    {
        $punishments = [];
        $tables = [
            'bans' => 'ban',
            'mutes' => 'mute',
            'warnings' => 'warning',
            'kicks' => 'kick'
        ];
        
        foreach ($tables as $table => $type) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                
                $stmt = $this->connection->prepare(
                    "SELECT *, 
                            '{$type}' as type,
                            created_at as time,
                            expires_at as until,
                            issued_by_name as staff_name
                     FROM {$fullTable} 
                     WHERE player_uuid = :uuid
                     ORDER BY created_at DESC"
                );
                $stmt->bindValue(':uuid', $playerUuid);
                $stmt->execute();
                
                $results = $stmt->fetchAll();
                
                // Convert timestamps if needed
                foreach ($results as &$row) {
                    if ($isMilliseconds) {
                        $row['time'] = isset($row['time']) ? (int)($row['time'] / 1000) : 0;
                        $row['until'] = isset($row['until']) ? (int)($row['until'] / 1000) : 0;
                    }
                }
                
                $punishments = array_merge($punishments, $results);
            } catch (PDOException $e) {
                error_log("Error getting player punishments from {$table}: " . $e->getMessage());
            }
        }
        
        // Sort by time descending
        usort($punishments, function($a, $b) {
            return ($b['time'] ?? 0) - ($a['time'] ?? 0);
        });
        
        return $punishments;
    }
    
    /**
     * Get staff notes for a player
     */
    public function getPlayerNotes(string $playerUuid): array
    {
        $notesTable = $this->tablePrefix . 'player_notes';
        
        try {
            // Check if table exists
            $stmt = $this->connection->query("SHOW TABLES LIKE '{$notesTable}'");
            if ($stmt->rowCount() === 0) {
                // Create table if not exists
                $this->createPlayerNotesTable();
            }
            
            $stmt = $this->connection->prepare(
                "SELECT * FROM {$notesTable} WHERE player_uuid = :uuid ORDER BY created_at DESC"
            );
            $stmt->bindValue(':uuid', $playerUuid);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting player notes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add staff note for a player
     */
    public function addPlayerNote(string $playerUuid, string $note, string $staffName): bool
    {
        $notesTable = $this->tablePrefix . 'player_notes';
        
        try {
            // Ensure table exists
            $this->createPlayerNotesTable();
            
            $stmt = $this->connection->prepare(
                "INSERT INTO {$notesTable} (player_uuid, note, staff_name, created_at) 
                 VALUES (:uuid, :note, :staff, :time)"
            );
            $stmt->bindValue(':uuid', $playerUuid);
            $stmt->bindValue(':note', $note);
            $stmt->bindValue(':staff', $staffName);
            $stmt->bindValue(':time', time(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding player note: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete staff note
     */
    public function deletePlayerNote(int $noteId): bool
    {
        $notesTable = $this->tablePrefix . 'player_notes';
        
        try {
            $stmt = $this->connection->prepare("DELETE FROM {$notesTable} WHERE id = :id");
            $stmt->bindValue(':id', $noteId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting player note: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create player notes table if not exists
     */
    private function createPlayerNotesTable(): void
    {
        $notesTable = $this->tablePrefix . 'player_notes';
        
        try {
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS {$notesTable} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    player_uuid VARCHAR(36) NOT NULL,
                    note TEXT NOT NULL,
                    staff_name VARCHAR(255) NOT NULL,
                    created_at BIGINT NOT NULL,
                    INDEX idx_player_uuid (player_uuid)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (PDOException $e) {
            error_log("Error creating player notes table: " . $e->getMessage());
        }
    }
    
    /**
     * Get staff statistics for admin panel
     */
    public function getStaffStatistics(int $limit = 20): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $staffStats = [];
        $currentTime = time();
        $thirtyDaysAgo = $currentTime - (30 * 86400);
        $sevenDaysAgo = $currentTime - (7 * 86400);
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                $thirtyDaysAgoTs = $isMilliseconds ? ($thirtyDaysAgo * 1000) : $thirtyDaysAgo;
                $sevenDaysAgoTs = $isMilliseconds ? ($sevenDaysAgo * 1000) : $sevenDaysAgo;
                
                $sql = "SELECT issued_by_name as staff_name, 
                               COUNT(*) as total,
                               SUM(CASE WHEN created_at >= {$sevenDaysAgoTs} THEN 1 ELSE 0 END) as last_7d,
                               SUM(CASE WHEN created_at >= {$thirtyDaysAgoTs} THEN 1 ELSE 0 END) as last_30d,
                               MAX(created_at) as last_action
                        FROM {$fullTable} 
                        WHERE issued_by_name IS NOT NULL 
                        AND issued_by_name != '' 
                        AND issued_by_name != 'Console'
                        GROUP BY issued_by_name";
                
                $stmt = $this->connection->query($sql);
                $results = $stmt->fetchAll();
                
                foreach ($results as $row) {
                    $staffName = $row['staff_name'];
                    if (!isset($staffStats[$staffName])) {
                        $staffStats[$staffName] = [
                            'staff_name' => $staffName,
                            'bans' => 0,
                            'mutes' => 0,
                            'warnings' => 0,
                            'kicks' => 0,
                            'total' => 0,
                            'last_7d' => 0,
                            'last_30d' => 0,
                            'last_action' => 0
                        ];
                    }
                    
                    $staffStats[$staffName][$table] = (int)$row['total'];
                    $staffStats[$staffName]['total'] += (int)$row['total'];
                    $staffStats[$staffName]['last_7d'] += (int)$row['last_7d'];
                    $staffStats[$staffName]['last_30d'] += (int)$row['last_30d'];
                    
                    $lastAction = (int)$row['last_action'];
                    if ($isMilliseconds) {
                        $lastAction = (int)($lastAction / 1000);
                    }
                    $staffStats[$staffName]['last_action'] = max(
                        $staffStats[$staffName]['last_action'],
                        $lastAction
                    );
                }
            } catch (PDOException $e) {
                error_log("Error getting staff stats for {$table}: " . $e->getMessage());
            }
        }
        
        // Sort by total descending
        usort($staffStats, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        return array_slice($staffStats, 0, $limit);
    }
    
    /**
     * Get single staff member statistics
     */
    public function getStaffMemberStats(string $staffName): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $currentTime = time();
        
        $stats = [
            'staff_name' => $staffName,
            'bans' => 0,
            'mutes' => 0,
            'warnings' => 0,
            'kicks' => 0,
            'total' => 0,
            'last_7d' => 0,
            'last_30d' => 0,
            'last_action' => 0,
            'avg_per_day_30d' => 0,
            'recent_punishments' => []
        ];
        
        $sevenDaysAgo = $currentTime - (7 * 86400);
        $thirtyDaysAgo = $currentTime - (30 * 86400);
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $isMilliseconds = $this->detectMillisecondTimestamps($fullTable);
                $sevenDaysAgoTs = $isMilliseconds ? ($sevenDaysAgo * 1000) : $sevenDaysAgo;
                $thirtyDaysAgoTs = $isMilliseconds ? ($thirtyDaysAgo * 1000) : $thirtyDaysAgo;
                
                $stmt = $this->connection->prepare(
                    "SELECT COUNT(*) as total,
                            SUM(CASE WHEN created_at >= :seven THEN 1 ELSE 0 END) as last_7d,
                            SUM(CASE WHEN created_at >= :thirty THEN 1 ELSE 0 END) as last_30d,
                            MAX(created_at) as last_action
                     FROM {$fullTable} 
                     WHERE issued_by_name = :staff"
                );
                $stmt->bindValue(':staff', $staffName);
                $stmt->bindValue(':seven', $sevenDaysAgoTs, PDO::PARAM_INT);
                $stmt->bindValue(':thirty', $thirtyDaysAgoTs, PDO::PARAM_INT);
                $stmt->execute();
                
                $row = $stmt->fetch();
                if ($row) {
                    $stats[$table] = (int)$row['total'];
                    $stats['total'] += (int)$row['total'];
                    $stats['last_7d'] += (int)$row['last_7d'];
                    $stats['last_30d'] += (int)$row['last_30d'];
                    
                    $lastAction = (int)$row['last_action'];
                    if ($isMilliseconds) {
                        $lastAction = (int)($lastAction / 1000);
                    }
                    $stats['last_action'] = max($stats['last_action'], $lastAction);
                }
                
                // Get recent punishments
                $stmt = $this->connection->prepare(
                    "SELECT *, '{$table}' as type, created_at as time
                     FROM {$fullTable} 
                     WHERE issued_by_name = :staff
                     ORDER BY created_at DESC
                     LIMIT 5"
                );
                $stmt->bindValue(':staff', $staffName);
                $stmt->execute();
                
                $recent = $stmt->fetchAll();
                foreach ($recent as &$r) {
                    if ($isMilliseconds && isset($r['time'])) {
                        $r['time'] = (int)($r['time'] / 1000);
                    }
                    $r['type'] = $table;
                }
                $stats['recent_punishments'] = array_merge($stats['recent_punishments'], $recent);
                
            } catch (PDOException $e) {
                error_log("Error getting staff member stats for {$table}: " . $e->getMessage());
            }
        }
        
        // Sort recent punishments by time
        usort($stats['recent_punishments'], function($a, $b) {
            return ($b['time'] ?? 0) - ($a['time'] ?? 0);
        });
        $stats['recent_punishments'] = array_slice($stats['recent_punishments'], 0, 10);
        
        // Calculate average per day
        if ($stats['last_30d'] > 0) {
            $stats['avg_per_day_30d'] = round($stats['last_30d'] / 30, 1);
        }
        
        return $stats;
    }
}

