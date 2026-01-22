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

class DetailController extends BaseController
{
    public function show(): void
    {
        $type = $_GET['type'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        
        $type = rtrim($type, 's');
        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'], true)) {
            $this->showError('Invalid punishment type');
            return;
        }
        
        if ($id <= 0) {
            $this->showError('Invalid punishment ID');
            return;
        }
        
        try {
            $tableName = $type . 's';
            $punishment = $this->repository->getPunishmentById($tableName, $id);
            
            if (!$punishment) {
                $this->showError($this->lang->get('error.punishment_not_found'));
                return;
            }
            
            $playerName = $punishment['player_name'] ?? $this->repository->getPlayerName($punishment['uuid'] ?? '');
            
            $formattedPunishment = $this->formatPunishmentDetail($punishment, $type, $playerName);
            
            $relatedPunishments = [];
            if (!empty($punishment['uuid'])) {
                $relatedPunishments = $this->repository->getPlayerPunishments($punishment['uuid']);
                $relatedPunishments = array_filter($relatedPunishments, function($p) use ($id, $tableName) {
                    return !($p['id'] == $id && $p['type'] == $tableName);
                });
            }
            
            $this->render('detail', [
                'title' => ucfirst($type) . ' #' . $id,
                'punishment' => $formattedPunishment,
                'relatedPunishments' => $this->formatPunishments($relatedPunishments),
                'type' => $type,
                'currentPage' => $tableName
            ]);
            
        } catch (Exception $e) {
            error_log("Error loading punishment detail: " . $e->getMessage());
            $this->showError($this->lang->get('error.loading_failed'));
        }
    }
    
    private function showError(string $message): void
    {
        $this->render('error', [
            'title' => $this->lang->get('error.not_found'),
            'message' => $message,
            'currentPage' => 'error'
        ]);
    }
    
    private function formatPunishmentDetail(array $punishment, string $type, ?string $playerName): array
    {
        $uuid = $punishment['uuid'] ?? '';
        $name = $playerName ?? 'Unknown';
        
        $duration = null;
        $timeLeft = null;
        $progress = 0;
        
        // HytaBans uses seconds timestamps, not milliseconds
        $time = (int)($punishment['time'] ?? 0);
        $until = (int)($punishment['until'] ?? 0);
        $currentTime = time();
        
        if (in_array($type, ['ban', 'mute']) && $until > 0) {
            $totalDuration = $until - $time;
            $duration = $this->formatDurationDetailed($totalDuration);
            
            if ($punishment['active'] && $until > $currentTime) {
                $timeLeftSec = $until - $currentTime;
                $timeLeft = $this->formatDurationDetailed($timeLeftSec);
                
                $elapsed = $currentTime - $time;
                $progress = min(100, max(0, ($elapsed / $totalDuration) * 100));
            } else {
                $timeLeft = $this->lang->get('punishment.expired');
                $progress = 100;
            }
        } elseif (in_array($type, ['ban', 'mute']) && isset($punishment['permanent']) && $punishment['permanent']) {
            $duration = $this->lang->get('punishment.permanent');
            $timeLeft = $this->lang->get('punishment.permanent');
        }
        
        $removed = (bool)($punishment['removed'] ?? false);
        $removedAt = null;
        if ($removed && isset($punishment['removed_at']) && $punishment['removed_at'] > 0) {
            $removedAt = $this->formatDate((int)$punishment['removed_at']);
        }
        
        return [
            'id' => (int)$punishment['id'],
            'uuid' => $uuid,
            'name' => SecurityManager::preventXss($name),
            'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
            'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
            'date' => $this->formatDate($time),
            'timestamp' => $time,
            'until' => $until > 0 && in_array($type, ['ban', 'mute']) ? $this->formatDate($until) : null,
            'until_timestamp' => in_array($type, ['ban', 'mute']) ? $until : 0,
            'duration' => $duration,
            'timeLeft' => $timeLeft,
            'progress' => $progress,
            'active' => (bool)($punishment['active'] ?? false),
            'removed' => $removed,
            'removed_by' => isset($punishment['removed_by_name']) 
                ? SecurityManager::preventXss($punishment['removed_by_name']) 
                : null,
            'removed_at' => $removedAt,
            'type' => $type
        ];
    }
    
    private function formatDurationDetailed(float $seconds): string
    {
        if ($seconds <= 0) return '0s';
        
        $intervals = [
            'y' => 31536000,
            'mo' => 2592000,
            'd' => 86400,
            'h' => 3600,
            'm' => 60,
            's' => 1
        ];
        
        $parts = [];
        
        foreach ($intervals as $unit => $value) {
            $count = floor($seconds / $value);
            if ($count > 0) {
                $parts[] = $count . $unit;
                $seconds = fmod($seconds, $value);
                
                if (count($parts) >= 2) break;
            }
        }
        
        return implode(' ', $parts);
    }
    
    protected function formatPunishments(array $punishments): array
    {
        return array_map(function($punishment) {
            $playerName = $punishment['player_name'] ?? $punishment['name'] ?? null;
            if (!$playerName && isset($punishment['uuid'])) {
                $playerName = $this->repository->getPlayerName($punishment['uuid']);
            }
            
            $time = (int)($punishment['time'] ?? $punishment['created_at'] ?? 0);
            $until = (int)($punishment['until'] ?? $punishment['expires_at'] ?? 0);
            
            return [
                'id' => (int)$punishment['id'],
                'type' => $punishment['type'] ?? 'unknown',
                'uuid' => $punishment['uuid'] ?? '',
                'name' => SecurityManager::preventXss($playerName ?? 'Unknown'),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? $punishment['issued_by_name'] ?? 'Console'),
                'date' => $this->formatDate($time),
                'until' => $until > 0 ? $this->formatDuration($until) : null,
                'active' => (bool)($punishment['active'] ?? false),
                'removed_by' => isset($punishment['removed_by_name']) ? SecurityManager::preventXss($punishment['removed_by_name']) : null
            ];
        }, $punishments);
    }
}
