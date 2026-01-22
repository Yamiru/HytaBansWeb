<?php
/**
 * ============================================================================
 *  HytaBansWeb
 * ============================================================================
 *
 *  Plugin Name:   HytaBansWeb
 *  Description:   A modern, secure, and responsive web interface for HytaBans punishment management system.
 *  Version:       3.0
 *  Repository:    https://github.com/Yamiru/HytaBansWeb
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

namespace core;

class EnvLoader
{
    private static bool $loaded = false;
    
    public static function load(?string $path = null): void
    {
        if (self::$loaded) {
            return;
        }
        
        self::loadFile($path);
        self::$loaded = true;
    }
    
    /**
     * Force reload .env file (useful after changes)
     */
    public static function reload(?string $path = null): void
    {
        // Clear cached values
        self::$values = [];
        self::$loaded = false;
        
        // Clear PHP's file stat cache for .env file
        $envFile = $path ?? dirname(__DIR__) . '/.env';
        clearstatcache(true, $envFile);
        
        self::loadFile($path);
        self::$loaded = true;
    }
    
    private static array $values = [];
    
    private static function loadFile(?string $path): void
    {
        $envFile = $path ?? dirname(__DIR__) . '/.env';
        
        if (!file_exists($envFile)) {
            throw new \RuntimeException('.env file not found at: ' . $envFile);
        }
        
        $content = file_get_contents($envFile);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            
            // Parse key=value (only split on first =)
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }
            
            $key = trim(substr($line, 0, $pos));
            $value = substr($line, $pos + 1);
            
            // Remove surrounding quotes if present (but keep content as-is)
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            
            // Store raw value without any processing
            self::$values[$key] = $value;
            $_ENV[$key] = $value;
        }
    }
    
    public static function get(string $key, mixed $default = null): mixed
    {
        // First check our internal storage (most reliable)
        if (isset(self::$values[$key])) {
            return self::$values[$key];
        }
        
        // Fallback to $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        return $default;
    }
}
