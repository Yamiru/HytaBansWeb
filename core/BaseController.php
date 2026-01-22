<?php
/**
 * ============================================================================
 *  HytaBansWeb
 * ============================================================================
 *
 *  Plugin Name:   HytaBansWeb
 *  Description:   A modern, secure, and responsive web interface for HytaBans punishment management system.
 *  Version:       3.0
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

abstract class BaseController
{
    protected DatabaseRepository $repository;
    protected LanguageManager $lang;
    protected ThemeManager $theme;
    protected array $config;
    
    public function __construct(DatabaseRepository $repository, LanguageManager $lang, ThemeManager $theme, array $config = [])
    {
        $this->repository = $repository;
        $this->lang = $lang;
        $this->theme = $theme;
        $this->config = $config;
    }
    
    protected function render(string $template, array $data = []): void
    {
        // Make controller instance available in templates
        $data['controller'] = $this;
        $data['lang'] = $this->lang;
        $data['theme'] = $this->theme;
        $data['config'] = $this->config;
        
        extract($data);
        
        include __DIR__ . "/../templates/header.php";
        include __DIR__ . "/../templates/{$template}.php";
        include __DIR__ . "/../templates/footer.php";
    }
    
    protected function renderPartial(string $template, array $data = []): string
    {
        ob_start();
        extract($data);
        include __DIR__ . "/../templates/partials/{$template}.php";
        return ob_get_clean();
    }
    
    protected function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }
    
    protected function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    protected function getPage(): int
    {
        $page = $_GET['page'] ?? 1;
        return max(1, SecurityManager::validateInteger($page, 1, 1000));
    }
    
    protected function getLimit(): int
    {
        return (int)($this->config['items_per_page'] ?? 20);
    }
    
    protected function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }
    
    protected function formatDate(int $timestamp): string
    {
        try {
            $timezone = new DateTimeZone($this->config['timezone'] ?? 'UTC');
            // Handle millisecond timestamps
            $seconds = intval($timestamp / 1000);
            $date = new DateTime('@' . $seconds);
            $date->setTimezone($timezone);
            
            return $date->format($this->config['date_format'] ?? 'Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s', intval($timestamp / 1000));
        }
    }
    
    protected function formatDuration(int $until): string
    {
        if ($until <= 0) {
            return $this->lang->get('punishment.permanent');
        }
        
        $now = time() * 1000;
        if ($until <= $now) {
            return $this->lang->get('punishment.expired');
        }
        
        $diff = intval(($until - $now) / 1000);
        $days = intval($diff / 86400);
        $hours = intval(($diff % 86400) / 3600);
        $minutes = intval(($diff % 3600) / 60);
        
        if ($days > 0) {
            return $this->lang->get('time.days', ['count' => (string)$days]);
        } elseif ($hours > 0) {
            return $this->lang->get('time.hours', ['count' => (string)$hours]);
        } else {
            return $this->lang->get('time.minutes', ['count' => (string)max(1, $minutes)]);
        }
    }
    
    /**
     * Get player icon - Hytale doesn't have avatar system like Minecraft
     * Returns a simple SVG icon with player initial
     */
    protected function getPlayerIcon(?string $name): string
    {
        // Generate color based on name
        $colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        $colorIndex = empty($name) ? 0 : ord(strtolower($name[0])) % count($colors);
        $color = $colors[$colorIndex];
        $initial = empty($name) ? '?' : strtoupper(substr($name, 0, 1));
        
        // Return inline SVG data URI with player initial
        return 'data:image/svg+xml,' . rawurlencode(
            '<svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">' .
            '<rect width="32" height="32" rx="6" fill="' . $color . '"/>' .
            '<text x="16" y="21" font-family="Arial,sans-serif" font-size="16" font-weight="bold" fill="white" text-anchor="middle">' . htmlspecialchars($initial) . '</text>' .
            '</svg>'
        );
    }
}