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

class HomeController extends BaseController
{
    public function index(): void
    {
        $stats = $this->repository->getStats();
        $recentBans = $this->repository->getBans(5, 0, true);
        $recentMutes = $this->repository->getMutes(5, 0, true);
        
        $recentBans = $this->ensurePlayerNames($recentBans);
        $recentMutes = $this->ensurePlayerNames($recentMutes);
        
        $searchQuery = $_GET['search'] ?? null;
        
        $this->render('home', [
            'stats' => $stats,
            'recentBans' => $recentBans,
            'recentMutes' => $recentMutes,
            'controller' => $this,
            'currentPage' => 'home',
            'searchQuery' => $searchQuery
        ]);
    }
    
    public function search(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSearch();
            return;
        }
        
        $this->render('search', [
            'currentPage' => 'search'
        ]);
    }
    
    private function handleSearch(): void
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid CSRF token'], 400);
            return;
        }
        
        $query = trim($_POST['query'] ?? '');
        if (empty($query) || strlen($query) < 1) {
            $this->jsonResponse(['error' => 'Search query must be at least 1 character'], 400);
            return;
        }
        
        $clientIp = SecurityManager::getClientIp();
        if (!SecurityManager::rateLimitCheck('search_' . $clientIp, 30, 60)) {
            $this->jsonResponse(['error' => 'Too many requests. Please try again later.'], 429);
            return;
        }
        
        $query = SecurityManager::sanitizeInput($query);
        
        try {
            $punishments = [];
            
            if (is_numeric($query)) {
                $punishments = $this->searchById((int)$query);
            }
            
            if (empty($punishments)) {
                $punishments = $this->repository->getPlayerPunishments($query);
            }
            
            if (empty($punishments)) {
                $punishments = $this->searchFlexible($query);
            }
            
            $this->jsonResponse([
                'success' => true,
                'player' => $query,
                'punishments' => $this->formatPunishmentsForSearch($punishments)
            ]);
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Search failed. Please try again.'], 500);
        }
    }
    
    private function searchById(int $id): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            $currentTime = time();
            
            foreach ($tables as $table) {
                $fullTable = $this->repository->getTablePrefix() . $table;
                
                if ($table === 'kicks') {
                    $sql = "SELECT '{$table}' as type, id, player_uuid as uuid, player_name,
                                   reason, issued_by_name as banned_by_name, created_at as time,
                                   0 as active
                            FROM {$fullTable} WHERE id = :id LIMIT 1";
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
                            FROM {$fullTable} WHERE id = :id LIMIT 1";
                }
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                if ($table !== 'kicks') {
                    $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
                }
                $stmt->execute();
                $result = $stmt->fetch();
                
                if ($result) {
                    $results[] = $result;
                }
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("ID search error: " . $e->getMessage());
            return [];
        }
    }
    
    private function searchFlexible(string $query): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            $currentTime = time();
            
            foreach ($tables as $table) {
                $fullTable = $this->repository->getTablePrefix() . $table;
                
                if ($table === 'kicks') {
                    $sql = "SELECT '{$table}' as type, id, player_uuid as uuid, player_name,
                                   reason, issued_by_name as banned_by_name, created_at as time,
                                   0 as active
                            FROM {$fullTable}
                            WHERE LOWER(player_name) LIKE LOWER(:query) 
                               OR LOWER(reason) LIKE LOWER(:query) 
                               OR LOWER(issued_by_name) LIKE LOWER(:query)
                               OR player_uuid LIKE :query2
                               OR id = :id
                            ORDER BY created_at DESC
                            LIMIT 50";
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
                            WHERE LOWER(player_name) LIKE LOWER(:query) 
                               OR LOWER(reason) LIKE LOWER(:query) 
                               OR LOWER(issued_by_name) LIKE LOWER(:query)
                               OR player_uuid LIKE :query2
                               OR id = :id
                            ORDER BY created_at DESC
                            LIMIT 50";
                }
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
                $stmt->bindValue(':query2', '%' . $query . '%', PDO::PARAM_STR);
                $stmt->bindValue(':id', is_numeric($query) ? (int)$query : 0, PDO::PARAM_INT);
                if ($table !== 'kicks') {
                    $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
                }
                $stmt->execute();
                
                $tableResults = $stmt->fetchAll();
                $results = array_merge($results, $tableResults);
            }
            
            usort($results, function($a, $b) {
                return ($b['time'] ?? 0) <=> ($a['time'] ?? 0);
            });
            
            return array_slice($results, 0, 100);
            
        } catch (Exception $e) {
            error_log("Flexible search error: " . $e->getMessage());
            return [];
        }
    }
    
    private function formatPunishmentsForSearch(array $punishments): array
    {
        return array_map(function($punishment) {
            $playerName = $punishment['player_name'] ?? 'Unknown';
            $time = $punishment['time'] ?? $punishment['created_at'] ?? 0;
            $until = $punishment['until'] ?? $punishment['expires_at'] ?? null;
            $staff = $punishment['banned_by_name'] ?? $punishment['issued_by_name'] ?? 'Console';
            
            $active = false;
            if (isset($punishment['removed']) && $punishment['removed'] == 0) {
                if (isset($punishment['permanent']) && $punishment['permanent'] == 1) {
                    $active = true;
                } elseif ($until === null || $until > time()) {
                    $active = true;
                }
            }
            if (isset($punishment['active'])) {
                $active = (bool)$punishment['active'];
            }
            
            return [
                'id' => $punishment['id'] ?? null,
                'type' => $punishment['type'] ?? 'unknown',
                'player_name' => SecurityManager::preventXss($playerName),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($staff),
                'date' => $this->formatDate((int)$time),
                'until' => $until !== null && $until > 0 ? $this->formatDuration((int)$until) : null,
                'active' => $active
            ];
        }, $punishments);
    }
    
    private function ensurePlayerNames(array $punishments): array
    {
        foreach ($punishments as &$punishment) {
            if (empty($punishment['player_name'])) {
                $punishment['player_name'] = 'Unknown';
            }
            
            if (empty($punishment['uuid'])) {
                $punishment['uuid'] = $punishment['player_uuid'] ?? '';
            }
            
            $punishment['reason'] = $punishment['reason'] ?? 'No reason provided';
            $punishment['time'] = $punishment['time'] ?? $punishment['created_at'] ?? 0;
            
            if (!isset($punishment['active'])) {
                $punishment['active'] = 0;
                if (isset($punishment['removed']) && $punishment['removed'] == 0) {
                    $until = $punishment['until'] ?? $punishment['expires_at'] ?? null;
                    if (isset($punishment['permanent']) && $punishment['permanent'] == 1) {
                        $punishment['active'] = 1;
                    } elseif ($until === null || $until > time()) {
                        $punishment['active'] = 1;
                    }
                }
            }
        }
        
        return $punishments;
    }
}
