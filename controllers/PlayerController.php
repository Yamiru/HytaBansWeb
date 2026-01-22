<?php
/**
 * ============================================================================
 *  HytaBansWeb - Player History Controller
 * ============================================================================
 */

declare(strict_types=1);

class PlayerController extends BaseController
{
    /**
     * Show player history page
     */
    public function show(string $identifier): void
    {
        try {
            // Get player info and punishment history
            $player = $this->repository->getPlayerByIdentifier($identifier);
            
            if (!$player) {
                $this->render('error', [
                    'title' => $this->lang->get('error.not_found'),
                    'message' => $this->lang->get('error.player_not_found')
                ]);
                return;
            }
            
            // Get all punishments for this player
            $punishments = $this->repository->getPlayerPunishmentsByUuid($player['player_uuid']);
            
            // Calculate statistics
            $stats = $this->calculatePlayerStats($punishments);
            
            // Get punishment timeline (for chart)
            $timeline = $this->getPunishmentTimeline($punishments);
            
            // Get staff notes if admin
            $notes = [];
            if ($this->isAdmin()) {
                $notes = $this->repository->getPlayerNotes($player['player_uuid']);
            }
            
            $this->render('player', [
                'title' => $player['player_name'] . ' - ' . $this->lang->get('nav.player_history'),
                'player' => $player,
                'punishments' => $punishments,
                'stats' => $stats,
                'timeline' => $timeline,
                'notes' => $notes,
                'currentPage' => 'player'
            ]);
            
        } catch (Exception $e) {
            error_log("Error loading player history: " . $e->getMessage());
            $this->render('error', [
                'title' => $this->lang->get('error.server_error'),
                'message' => $this->lang->get('error.loading_failed')
            ]);
        }
    }
    
    /**
     * Add staff note (admin only)
     */
    public function addNote(): void
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        if (!isset($_POST['csrf_token']) || !SecurityManager::validateCsrfToken($_POST['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
            return;
        }
        
        $playerUuid = $_POST['player_uuid'] ?? '';
        $note = trim($_POST['note'] ?? '');
        
        if (empty($playerUuid) || empty($note)) {
            $this->jsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
            return;
        }
        
        try {
            $staffName = $_SESSION['admin_username'] ?? 'Admin';
            $success = $this->repository->addPlayerNote($playerUuid, $note, $staffName);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Note added successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to add note'], 500);
            }
        } catch (Exception $e) {
            error_log("Error adding player note: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }
    
    /**
     * Delete staff note (admin only)
     */
    public function deleteNote(): void
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        if (!isset($_POST['csrf_token']) || !SecurityManager::validateCsrfToken($_POST['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
            return;
        }
        
        $noteId = (int)($_POST['note_id'] ?? 0);
        
        if ($noteId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid note ID'], 400);
            return;
        }
        
        try {
            $success = $this->repository->deletePlayerNote($noteId);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Note deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete note'], 500);
            }
        } catch (Exception $e) {
            error_log("Error deleting player note: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Server error'], 500);
        }
    }
    
    /**
     * Calculate player statistics
     */
    private function calculatePlayerStats(array $punishments): array
    {
        $stats = [
            'total' => count($punishments),
            'bans' => 0,
            'bans_active' => 0,
            'mutes' => 0,
            'mutes_active' => 0,
            'warnings' => 0,
            'kicks' => 0,
            'first_punishment' => null,
            'last_punishment' => null,
            'most_common_reason' => null,
            'most_active_staff' => null
        ];
        
        $reasons = [];
        $staff = [];
        $currentTime = time();
        
        foreach ($punishments as $p) {
            $type = $p['type'] ?? 'unknown';
            
            // Count by type
            if ($type === 'ban') {
                $stats['bans']++;
                if ($this->isPunishmentActive($p, $currentTime)) {
                    $stats['bans_active']++;
                }
            } elseif ($type === 'mute') {
                $stats['mutes']++;
                if ($this->isPunishmentActive($p, $currentTime)) {
                    $stats['mutes_active']++;
                }
            } elseif ($type === 'warning') {
                $stats['warnings']++;
            } elseif ($type === 'kick') {
                $stats['kicks']++;
            }
            
            // Track first/last
            $time = (int)($p['time'] ?? 0);
            if ($stats['first_punishment'] === null || $time < $stats['first_punishment']) {
                $stats['first_punishment'] = $time;
            }
            if ($stats['last_punishment'] === null || $time > $stats['last_punishment']) {
                $stats['last_punishment'] = $time;
            }
            
            // Track reasons
            $reason = $p['reason'] ?? '';
            if (!empty($reason)) {
                $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
            }
            
            // Track staff
            $staffName = $p['staff_name'] ?? $p['banned_by_name'] ?? '';
            if (!empty($staffName) && strtolower($staffName) !== 'console') {
                $staff[$staffName] = ($staff[$staffName] ?? 0) + 1;
            }
        }
        
        // Find most common reason
        if (!empty($reasons)) {
            arsort($reasons);
            $stats['most_common_reason'] = array_key_first($reasons);
        }
        
        // Find most active staff
        if (!empty($staff)) {
            arsort($staff);
            $stats['most_active_staff'] = array_key_first($staff);
        }
        
        return $stats;
    }
    
    /**
     * Check if punishment is active
     */
    private function isPunishmentActive(array $p, int $currentTime): bool
    {
        if (($p['removed'] ?? 0) == 1) {
            return false;
        }
        
        if (($p['permanent'] ?? 0) == 1) {
            return true;
        }
        
        $expiresAt = (int)($p['until'] ?? $p['expires_at'] ?? 0);
        
        if ($expiresAt === 0 || $expiresAt === -1) {
            return true;
        }
        
        // Check for milliseconds
        if ($expiresAt > 10000000000) {
            $expiresAt = (int)($expiresAt / 1000);
        }
        
        return $expiresAt > $currentTime;
    }
    
    /**
     * Get punishment timeline for chart
     */
    private function getPunishmentTimeline(array $punishments): array
    {
        $timeline = [];
        
        // Group by month
        foreach ($punishments as $p) {
            $time = (int)($p['time'] ?? 0);
            if ($time > 10000000000) {
                $time = (int)($time / 1000);
            }
            
            $monthKey = date('Y-m', $time);
            $type = $p['type'] ?? 'unknown';
            
            if (!isset($timeline[$monthKey])) {
                $timeline[$monthKey] = [
                    'month' => $monthKey,
                    'label' => date('M Y', $time),
                    'bans' => 0,
                    'mutes' => 0,
                    'warnings' => 0,
                    'kicks' => 0,
                    'total' => 0
                ];
            }
            
            if (isset($timeline[$monthKey][$type])) {
                $timeline[$monthKey][$type]++;
            }
            $timeline[$monthKey]['total']++;
        }
        
        // Sort by month and return last 12 months
        ksort($timeline);
        return array_slice(array_values($timeline), -12);
    }
    
    /**
     * Check if current user is admin
     */
    private function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
    }
}
