<?php
/**
 * ============================================================================
 *  HytaBansWeb - Installation Wizard
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

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Load version from .version file
$version = file_exists(__DIR__ . '/.version') ? trim(file_get_contents(__DIR__ . '/.version')) : '1.0';

// Initialize step
if (!isset($_SESSION['install_step'])) {
    $_SESSION['install_step'] = 1;
}

// Reset
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: install.php');
    exit;
}

// Test database connection
function testDbConnection($host, $port, $name, $user, $pass) {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Check if tables exist
function checkTables($pdo, $prefix) {
    $tables = ['bans', 'mutes', 'warnings', 'kicks', 'logs'];
    $found = [];
    $missing = [];
    
    foreach ($tables as $table) {
        $tableName = $prefix . $table;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$tableName}'");
            if ($stmt->rowCount() > 0) {
                $found[] = $table;
            } else {
                $missing[] = $table;
            }
        } catch (PDOException $e) {
            $missing[] = $table;
        }
    }
    
    return ['found' => $found, 'missing' => $missing];
}

// Handle AJAX database test
if (isset($_POST['ajax_test_db'])) {
    header('Content-Type: application/json');
    
    $result = testDbConnection(
        $_POST['db_host'] ?? 'localhost',
        $_POST['db_port'] ?? '3306',
        $_POST['db_name'] ?? '',
        $_POST['db_user'] ?? '',
        $_POST['db_pass'] ?? ''
    );
    
    if ($result['success']) {
        $tables = checkTables($result['pdo'], $_POST['table_prefix'] ?? 'hb_');
        echo json_encode([
            'success' => true,
            'message' => 'Pripojenie k databáze úspešné!',
            'tables' => $tables
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Chyba pripojenia: ' . $result['error']
        ]);
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_test_db'])) {
    if ($_SESSION['install_step'] == 1) {
        // Requirements check passed
        $_SESSION['install_step'] = 2;
    } elseif ($_SESSION['install_step'] == 2) {
        // Database config
        $_SESSION['config'] = $_POST;
        
        // Test connection before proceeding
        $result = testDbConnection(
            $_POST['db_host'] ?? 'localhost',
            $_POST['db_port'] ?? '3306',
            $_POST['db_name'] ?? '',
            $_POST['db_user'] ?? '',
            $_POST['db_pass'] ?? ''
        );
        
        if ($result['success']) {
            $_SESSION['install_step'] = 3;
        } else {
            $_SESSION['db_error'] = $result['error'];
        }
    } elseif ($_SESSION['install_step'] == 3) {
        // Site config
        $_SESSION['config'] = array_merge($_SESSION['config'] ?? [], $_POST);
        $_SESSION['install_step'] = 4;
    } elseif ($_SESSION['install_step'] == 4) {
        // Final - generate .env
        $_SESSION['config'] = array_merge($_SESSION['config'] ?? [], $_POST);
        $_SESSION['install_step'] = 5;
    }
    
    header('Location: install.php');
    exit;
}

$step = $_SESSION['install_step'];
$dbError = $_SESSION['db_error'] ?? null;
unset($_SESSION['db_error']);

function getVal($name, $default = '') {
    return htmlspecialchars($_SESSION['config'][$name] ?? $default, ENT_QUOTES, 'UTF-8');
}

function generateEnv($config) {
    global $version;
    
    $env = "# ============================================================================
# HytaBansWeb {$version} - Configuration File
# Generated: " . date('Y-m-d H:i:s') . "
# ============================================================================

# Database Configuration
DB_HOST=" . ($config['db_host'] ?? 'localhost') . "
DB_PORT=" . ($config['db_port'] ?? '3306') . "
DB_NAME=" . ($config['db_name'] ?? '') . "
DB_USER=" . ($config['db_user'] ?? '') . "
DB_PASS=" . ($config['db_pass'] ?? '') . "
DB_DRIVER=mysql
TABLE_PREFIX=" . ($config['table_prefix'] ?? 'hb_') . "

# Site Configuration
SITE_NAME=" . ($config['site_name'] ?? 'HytaBansWeb') . "
FOOTER_SITE_NAME=" . ($config['footer_site_name'] ?? '') . "
ITEMS_PER_PAGE=" . ($config['items_per_page'] ?? '100') . "
TIMEZONE=" . ($config['timezone'] ?? 'UTC') . "
DATE_FORMAT=Y-m-d H:i:s
BASE_URL=" . ($config['base_url'] ?? '') . "

# Default Settings
DEFAULT_THEME=" . ($config['default_theme'] ?? 'dark') . "
DEFAULT_LANGUAGE=" . ($config['default_language'] ?? 'en') . "
SHOW_PLAYER_UUID=" . ($config['show_player_uuid'] ?? 'false') . "

# Debug Mode
DEBUG=false
LOG_ERRORS=true
ERROR_LOG_PATH=logs/error.log

# Security
SESSION_LIFETIME=7200
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_WINDOW=3600

# Admin Configuration
ADMIN_ENABLED=true
ADMIN_PASSWORD=

# Allow password login
ALLOW_PASSWORD_LOGIN=true

# Google OAuth Configuration
GOOGLE_AUTH_ENABLED=" . ($config['google_auth_enabled'] ?? 'false') . "
GOOGLE_CLIENT_ID=" . ($config['google_client_id'] ?? '') . "
GOOGLE_CLIENT_SECRET=" . ($config['google_client_secret'] ?? '') . "

# Discord OAuth Configuration
DISCORD_AUTH_ENABLED=" . ($config['discord_auth_enabled'] ?? 'false') . "
DISCORD_CLIENT_ID=" . ($config['discord_client_id'] ?? '') . "
DISCORD_CLIENT_SECRET=" . ($config['discord_client_secret'] ?? '') . "

# Contact Configuration
PROTEST_DISCORD=" . ($config['protest_discord'] ?? '') . "
PROTEST_EMAIL=" . ($config['protest_email'] ?? '') . "
PROTEST_FORUM=" . ($config['protest_forum'] ?? '') . "

# Display Options
SHOW_SILENT_PUNISHMENTS=" . ($config['show_silent_punishments'] ?? 'true') . "
SHOW_SERVER_ORIGIN=" . ($config['show_server_origin'] ?? 'false') . "
SHOW_SERVER_SCOPE=" . ($config['show_server_scope'] ?? 'false') . "
SHOW_CONTACT_DISCORD=" . ($config['show_contact_discord'] ?? 'true') . "
SHOW_CONTACT_EMAIL=" . ($config['show_contact_email'] ?? 'true') . "
SHOW_CONTACT_FORUM=" . ($config['show_contact_forum'] ?? 'true') . "

# SEO Configuration
SITE_URL=" . ($config['site_url'] ?? '') . "
SITE_LANG=" . ($config['site_lang'] ?? 'en') . "
SITE_CHARSET=UTF-8
SITE_VIEWPORT=width=device-width, initial-scale=1.0
SITE_ROBOTS=" . ($config['site_robots'] ?? 'index, follow') . "
SITE_DESCRIPTION=" . ($config['site_description'] ?? 'View and search player punishments on our Hytale server') . "
SITE_TITLE_TEMPLATE={page} - {site}
SITE_THEME_COLOR=" . ($config['site_theme_color'] ?? '#6366f1') . "
SITE_OG_IMAGE=" . ($config['site_og_image'] ?? '') . "
SITE_TWITTER_SITE=" . ($config['site_twitter_site'] ?? '') . "
SITE_KEYWORDS=" . ($config['site_keywords'] ?? 'hytale,hytabans,punishments,bans,mutes,server') . "

# SEO Advanced
SEO_ENABLE_SCHEMA=" . ($config['seo_enable_schema'] ?? 'true') . "
SEO_ORGANIZATION_NAME=" . ($config['seo_organization_name'] ?? '') . "
SEO_ORGANIZATION_LOGO=" . ($config['seo_organization_logo'] ?? '') . "
SEO_SOCIAL_FACEBOOK=" . ($config['seo_social_facebook'] ?? '') . "
SEO_SOCIAL_TWITTER=" . ($config['seo_social_twitter'] ?? '') . "
SEO_SOCIAL_YOUTUBE=" . ($config['seo_social_youtube'] ?? '') . "
SEO_ENABLE_BREADCRUMBS=true
SEO_ENABLE_SITEMAP=true
SEO_CONTACT_TYPE=CustomerService
SEO_CONTACT_PHONE=" . ($config['seo_contact_phone'] ?? '') . "
SEO_CONTACT_EMAIL=" . ($config['seo_contact_email'] ?? '') . "
SEO_PRICE_CURRENCY=EUR
SEO_LOCALE=" . ($config['seo_locale'] ?? 'en_US') . "
SEO_AI_TRAINING=" . ($config['seo_ai_training'] ?? 'true') . "
SEO_GEO_REGION=" . ($config['seo_geo_region'] ?? '') . "
SEO_GEO_PLACENAME=" . ($config['seo_geo_placename'] ?? '') . "
SEO_GEO_POSITION=
SEO_FACEBOOK_APP_ID=
SEO_TWITTER_CREATOR=

# Require Login
REQUIRE_LOGIN=false

# Menu Display
SHOW_MENU_PROTEST=" . ($config['show_menu_protest'] ?? 'true') . "
SHOW_MENU_STATS=" . ($config['show_menu_stats'] ?? 'true') . "
SHOW_MENU_ADMIN=true

# Performance & Cache
CACHE_ENABLED=true
CACHE_LIFETIME=3600

# Demo Mode
DEMO_MODE=false
";
    return $env;
}

// Check requirements
function checkRequirements() {
    $requirements = [];
    
    // PHP Version
    $requirements['php_version'] = [
        'name' => 'PHP Version',
        'required' => '8.0+',
        'current' => PHP_VERSION,
        'ok' => version_compare(PHP_VERSION, '8.0.0', '>=')
    ];
    
    // PDO Extension
    $requirements['pdo'] = [
        'name' => 'PDO Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
        'ok' => extension_loaded('pdo')
    ];
    
    // PDO MySQL
    $requirements['pdo_mysql'] = [
        'name' => 'PDO MySQL',
        'required' => 'Enabled',
        'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
        'ok' => extension_loaded('pdo_mysql')
    ];
    
    // JSON Extension
    $requirements['json'] = [
        'name' => 'JSON Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('json') ? 'Enabled' : 'Disabled',
        'ok' => extension_loaded('json')
    ];
    
    // Mbstring Extension
    $requirements['mbstring'] = [
        'name' => 'Mbstring Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
        'ok' => extension_loaded('mbstring')
    ];
    
    // OpenSSL Extension
    $requirements['openssl'] = [
        'name' => 'OpenSSL Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('openssl') ? 'Enabled' : 'Disabled',
        'ok' => extension_loaded('openssl')
    ];
    
    // Writable directories
    $requirements['data_writable'] = [
        'name' => 'Data Directory',
        'required' => 'Writable',
        'current' => is_writable(__DIR__ . '/data') ? 'Writable' : 'Not Writable',
        'ok' => is_writable(__DIR__ . '/data')
    ];
    
    // Root directory writable (for .env)
    $requirements['root_writable'] = [
        'name' => 'Root Directory (.env)',
        'required' => 'Writable',
        'current' => is_writable(__DIR__) ? 'Writable' : 'Not Writable',
        'ok' => is_writable(__DIR__)
    ];
    
    return $requirements;
}

$requirements = checkRequirements();
$allRequirementsMet = !in_array(false, array_column($requirements, 'ok'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HytaBansWeb <?= $version ?> - Installation Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --bg-dark: #09090b;
            --bg-card: #18181b;
            --bg-input: #27272a;
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --border: #3f3f46;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%);
            min-height: 100vh;
            color: var(--text-primary);
            padding: 2rem 1rem;
        }
        
        .installer-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .installer-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .installer-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .installer-header .version {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .card-header {
            background: rgba(124, 58, 237, 0.15);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-primary);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 60%;
            width: 80%;
            height: 2px;
            background: var(--border);
        }
        
        .step.completed:not(:last-child)::after {
            background: var(--success);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-dark);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            color: var(--text-primary);
        }
        
        .step.active .step-number {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        
        .step.completed .step-number {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }
        
        .step-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-align: center;
        }
        
        .step.active .step-label,
        .step.completed .step-label {
            color: var(--text-primary);
        }
        
        /* Form Styles */
        .form-label {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        
        .form-control:focus, .form-select:focus {
            background: var(--bg-dark);
            border-color: var(--primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .form-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .form-section {
            background: rgba(0,0,0,0.2);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-section h5 {
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4338ca);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }
        
        .btn-outline-secondary {
            border-color: var(--border);
            color: var(--text-secondary);
        }
        
        .btn-outline-secondary:hover {
            background: var(--bg-dark);
            border-color: var(--primary);
            color: var(--primary);
        }
        
        /* Requirements Table */
        .requirements-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .requirements-table th,
        .requirements-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        .requirements-table th {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .status-ok {
            color: var(--success);
        }
        
        .status-error {
            color: var(--danger);
        }
        
        /* Alert customization */
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fcd34d;
        }
        
        .alert-info {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
        }
        
        /* Code block */
        .code-block {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 1rem;
            font-family: 'Fira Code', monospace;
            font-size: 0.875rem;
            max-height: 400px;
            overflow-y: auto;
            color: var(--text-primary);
        }
        
        .code-block pre {
            color: #e2e8f0;
            margin: 0;
        }
        
        code {
            background: rgba(124, 58, 237, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            color: #c4b5fd;
        }
        
        /* Connection test result */
        #connection-result {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            display: none;
            color: var(--text-primary);
        }
        
        #connection-result.success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid var(--success);
            display: block;
            color: #86efac;
        }
        
        #connection-result.success strong {
            color: #4ade80;
        }
        
        #connection-result .text-success {
            color: #86efac !important;
        }
        
        #connection-result .text-warning {
            color: #fde047 !important;
        }
        
        #connection-result.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--danger);
            display: block;
            color: #fca5a5;
        }
        
        #connection-result.error strong {
            color: #f87171;
        }
        
        /* Table text colors */
        .requirements-table td {
            color: var(--text-primary);
        }
        
        /* Alert text improvements */
        .alert h5 {
            color: inherit;
        }
        
        .alert ol, .alert li {
            color: inherit;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .progress-steps {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .step:not(:last-child)::after {
                display: none;
            }
            
            .installer-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><i class="fas fa-shield-halved"></i> HytaBansWeb</h1>
            <p class="version">Version <?= $version ?> - Installation Wizard</p>
        </div>
        
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>">
                <div class="step-number"><?= $step > 1 ? '<i class="fas fa-check"></i>' : '1' ?></div>
                <span class="step-label">Requirements</span>
            </div>
            <div class="step <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>">
                <div class="step-number"><?= $step > 2 ? '<i class="fas fa-check"></i>' : '2' ?></div>
                <span class="step-label">Database</span>
            </div>
            <div class="step <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>">
                <div class="step-number"><?= $step > 3 ? '<i class="fas fa-check"></i>' : '3' ?></div>
                <span class="step-label">Site Config</span>
            </div>
            <div class="step <?= $step >= 4 ? ($step > 4 ? 'completed' : 'active') : '' ?>">
                <div class="step-number"><?= $step > 4 ? '<i class="fas fa-check"></i>' : '4' ?></div>
                <span class="step-label">SEO & Options</span>
            </div>
            <div class="step <?= $step >= 5 ? 'active' : '' ?>">
                <div class="step-number">5</div>
                <span class="step-label">Complete</span>
            </div>
        </div>
        
        <div class="card">
            <?php if ($step == 1): ?>
            <!-- Step 1: Requirements Check -->
            <div class="card-header">
                <h2><i class="fas fa-clipboard-check"></i> System Requirements</h2>
            </div>
            <div class="card-body">
                <table class="requirements-table">
                    <thead>
                        <tr>
                            <th>Requirement</th>
                            <th>Required</th>
                            <th>Current</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requirements as $req): ?>
                        <tr>
                            <td><?= $req['name'] ?></td>
                            <td><?= $req['required'] ?></td>
                            <td><?= $req['current'] ?></td>
                            <td>
                                <?php if ($req['ok']): ?>
                                <span class="status-ok"><i class="fas fa-check-circle"></i> OK</span>
                                <?php else: ?>
                                <span class="status-error"><i class="fas fa-times-circle"></i> Failed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($allRequirementsMet): ?>
                <div class="alert alert-success mt-4">
                    <i class="fas fa-check-circle"></i> All requirements met! You can proceed with the installation.
                </div>
                <form method="POST" class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-right"></i> Continue to Database Setup
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-danger mt-4">
                    <i class="fas fa-exclamation-triangle"></i> Some requirements are not met. Please fix them before continuing.
                </div>
                <?php endif; ?>
            </div>
            
            <?php elseif ($step == 2): ?>
            <!-- Step 2: Database Configuration -->
            <div class="card-header">
                <h2><i class="fas fa-database"></i> Database Configuration</h2>
            </div>
            <div class="card-body">
                <?php if ($dbError): ?>
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-triangle"></i> Database connection failed: <?= htmlspecialchars($dbError) ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" id="db-form">
                    <div class="form-section">
                        <h5><i class="fas fa-server"></i> Database Connection</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Host</label>
                                <input type="text" class="form-control" name="db_host" value="<?= getVal('db_host', 'localhost') ?>" required>
                                <small class="form-text">Usually "localhost" or IP address</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Port</label>
                                <input type="text" class="form-control" name="db_port" value="<?= getVal('db_port', '3306') ?>" required>
                                <small class="form-text">Default MySQL port is 3306</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Name</label>
                                <input type="text" class="form-control" name="db_name" value="<?= getVal('db_name') ?>" required>
                                <small class="form-text">HytaBans database name</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Table Prefix</label>
                                <input type="text" class="form-control" name="table_prefix" value="<?= getVal('table_prefix', 'hb_') ?>" required>
                                <small class="form-text">HytaBans table prefix (default: hb_)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Username</label>
                                <input type="text" class="form-control" name="db_user" value="<?= getVal('db_user') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Password</label>
                                <input type="password" class="form-control" name="db_pass" value="<?= getVal('db_pass') ?>">
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-secondary" id="test-connection">
                            <i class="fas fa-plug"></i> Test Connection
                        </button>
                        
                        <div id="connection-result"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?reset" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Start Over
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Continue to Site Setup
                        </button>
                    </div>
                </form>
            </div>
            
            <?php elseif ($step == 3): ?>
            <!-- Step 3: Site Configuration -->
            <div class="card-header">
                <h2><i class="fas fa-cog"></i> Site Configuration</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-section">
                        <h5><i class="fas fa-globe"></i> Basic Settings</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" value="<?= getVal('site_name', 'HytaBansWeb') ?>" required>
                                <small class="form-text">Display name for your site</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Footer Site Name</label>
                                <input type="text" class="form-control" name="footer_site_name" value="<?= getVal('footer_site_name') ?>">
                                <small class="form-text">Server name in footer (optional)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Site URL</label>
                                <input type="url" class="form-control" name="site_url" value="<?= getVal('site_url') ?>" placeholder="https://yourdomain.com">
                                <small class="form-text">Full URL with https://</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Default Theme</label>
                                <select class="form-select" name="default_theme">
                                    <option value="dark" <?= getVal('default_theme', 'dark') === 'dark' ? 'selected' : '' ?>>Dark</option>
                                    <option value="light" <?= getVal('default_theme') === 'light' ? 'selected' : '' ?>>Light</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Default Language</label>
                                <select class="form-select" name="default_language">
                                    <option value="en" <?= getVal('default_language', 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="sk" <?= getVal('default_language') === 'sk' ? 'selected' : '' ?>>Slovenčina</option>
                                    <option value="cs" <?= getVal('default_language') === 'cs' ? 'selected' : '' ?>>Čeština</option>
                                    <option value="de" <?= getVal('default_language') === 'de' ? 'selected' : '' ?>>Deutsch</option>
                                    <option value="es" <?= getVal('default_language') === 'es' ? 'selected' : '' ?>>Español</option>
                                    <option value="fr" <?= getVal('default_language') === 'fr' ? 'selected' : '' ?>>Français</option>
                                    <option value="pl" <?= getVal('default_language') === 'pl' ? 'selected' : '' ?>>Polski</option>
                                    <option value="ru" <?= getVal('default_language') === 'ru' ? 'selected' : '' ?>>Русский</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-select" name="timezone">
                                    <option value="UTC" <?= getVal('timezone', 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                    <option value="Europe/Bratislava" <?= getVal('timezone') === 'Europe/Bratislava' ? 'selected' : '' ?>>Europe/Bratislava</option>
                                    <option value="Europe/Prague" <?= getVal('timezone') === 'Europe/Prague' ? 'selected' : '' ?>>Europe/Prague</option>
                                    <option value="Europe/London" <?= getVal('timezone') === 'Europe/London' ? 'selected' : '' ?>>Europe/London</option>
                                    <option value="America/New_York" <?= getVal('timezone') === 'America/New_York' ? 'selected' : '' ?>>America/New_York</option>
                                    <option value="America/Los_Angeles" <?= getVal('timezone') === 'America/Los_Angeles' ? 'selected' : '' ?>>America/Los_Angeles</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" class="form-control" name="items_per_page" value="<?= getVal('items_per_page', '100') ?>" min="10" max="500">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Player UUID</label>
                                <select class="form-select" name="show_player_uuid">
                                    <option value="false" <?= getVal('show_player_uuid', 'false') === 'false' ? 'selected' : '' ?>>No</option>
                                    <option value="true" <?= getVal('show_player_uuid') === 'true' ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h5><i class="fas fa-key"></i> OAuth Authentication</h5>
                        <p class="text-muted small mb-3">Optional: Enable social login for admin panel</p>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Google OAuth</label>
                                <select class="form-select" name="google_auth_enabled" id="google_auth_enabled">
                                    <option value="false" <?= getVal('google_auth_enabled', 'false') === 'false' ? 'selected' : '' ?>>Disabled</option>
                                    <option value="true" <?= getVal('google_auth_enabled') === 'true' ? 'selected' : '' ?>>Enabled</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Google Client ID</label>
                                <input type="text" class="form-control" name="google_client_id" value="<?= getVal('google_client_id') ?>" placeholder="xxx.apps.googleusercontent.com">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Google Client Secret</label>
                                <input type="password" class="form-control" name="google_client_secret" value="<?= getVal('google_client_secret') ?>" placeholder="GOCSPX-xxx">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discord OAuth</label>
                                <select class="form-select" name="discord_auth_enabled" id="discord_auth_enabled">
                                    <option value="false" <?= getVal('discord_auth_enabled', 'false') === 'false' ? 'selected' : '' ?>>Disabled</option>
                                    <option value="true" <?= getVal('discord_auth_enabled') === 'true' ? 'selected' : '' ?>>Enabled</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discord Client ID</label>
                                <input type="text" class="form-control" name="discord_client_id" value="<?= getVal('discord_client_id') ?>" placeholder="123456789012345678">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discord Client Secret</label>
                                <input type="password" class="form-control" name="discord_client_secret" value="<?= getVal('discord_client_secret') ?>" placeholder="xxxxxxxxxxxxxx">
                            </div>
                        </div>
                        
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-info-circle"></i> <strong>Redirect URIs:</strong><br>
                            Google: <code>{your-site}/admin/callback/google</code><br>
                            Discord: <code>{your-site}/admin/oauth-callback?provider=discord</code>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h5><i class="fas fa-envelope"></i> Contact Configuration</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discord Invite</label>
                                <input type="url" class="form-control" name="protest_discord" value="<?= getVal('protest_discord') ?>" placeholder="https://discord.gg/...">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="protest_email" value="<?= getVal('protest_email') ?>" placeholder="support@yourdomain.com">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Forum URL</label>
                                <input type="url" class="form-control" name="protest_forum" value="<?= getVal('protest_forum') ?>" placeholder="https://forum.yourdomain.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?reset" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Start Over
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Continue to SEO Options
                        </button>
                    </div>
                </form>
            </div>
            
            <?php elseif ($step == 4): ?>
            <!-- Step 4: SEO & Display Options -->
            <div class="card-header">
                <h2><i class="fas fa-search"></i> SEO & Display Options</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-section">
                        <h5><i class="fas fa-search-plus"></i> SEO Configuration</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Site Description</label>
                                <textarea class="form-control" name="site_description" rows="2"><?= getVal('site_description', 'View and search player punishments on our Hytale server') ?></textarea>
                                <small class="form-text">Meta description for search engines (150-160 characters)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keywords</label>
                                <input type="text" class="form-control" name="site_keywords" value="<?= getVal('site_keywords', 'hytale,hytabans,punishments,bans,mutes,server') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Theme Color</label>
                                <input type="color" class="form-control form-control-color" name="site_theme_color" value="<?= getVal('site_theme_color', '#6366f1') ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SEO Locale</label>
                                <input type="text" class="form-control" name="seo_locale" value="<?= getVal('seo_locale', 'en_US') ?>" placeholder="en_US">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Organization Name</label>
                                <input type="text" class="form-control" name="seo_organization_name" value="<?= getVal('seo_organization_name') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Enable Schema.org</label>
                                <select class="form-select" name="seo_enable_schema">
                                    <option value="true" <?= getVal('seo_enable_schema', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('seo_enable_schema') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h5><i class="fas fa-eye"></i> Display Options</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Silent Punishments</label>
                                <select class="form-select" name="show_silent_punishments">
                                    <option value="true" <?= getVal('show_silent_punishments', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_silent_punishments') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Server Origin</label>
                                <select class="form-select" name="show_server_origin">
                                    <option value="true" <?= getVal('show_server_origin') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_server_origin', 'false') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Server Scope</label>
                                <select class="form-select" name="show_server_scope">
                                    <option value="true" <?= getVal('show_server_scope') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_server_scope', 'false') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Discord Contact</label>
                                <select class="form-select" name="show_contact_discord">
                                    <option value="true" <?= getVal('show_contact_discord', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_contact_discord') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Email Contact</label>
                                <select class="form-select" name="show_contact_email">
                                    <option value="true" <?= getVal('show_contact_email', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_contact_email') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Forum Contact</label>
                                <select class="form-select" name="show_contact_forum">
                                    <option value="true" <?= getVal('show_contact_forum', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_contact_forum') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h5><i class="fas fa-bars"></i> Menu Options</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Protest Menu</label>
                                <select class="form-select" name="show_menu_protest">
                                    <option value="true" <?= getVal('show_menu_protest', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_menu_protest') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Statistics Menu</label>
                                <select class="form-select" name="show_menu_stats">
                                    <option value="true" <?= getVal('show_menu_stats', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('show_menu_stats') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Allow AI Training</label>
                                <select class="form-select" name="seo_ai_training">
                                    <option value="true" <?= getVal('seo_ai_training', 'true') === 'true' ? 'selected' : '' ?>>Yes</option>
                                    <option value="false" <?= getVal('seo_ai_training') === 'false' ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?reset" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Start Over
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Generate Configuration
                        </button>
                    </div>
                </form>
            </div>
            
            <?php elseif ($step == 5): ?>
            <!-- Step 5: Complete -->
            <div class="card-header">
                <h2><i class="fas fa-check-circle"></i> Installation Complete!</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i class="fas fa-clipboard-check"></i> Next Steps:</h5>
                    <ol class="mb-0">
                        <li>Copy the .env content below and save it as <code>.env</code> file in your root directory</li>
                        <li>Set admin password using <code>hash.php</code></li>
                        <li><strong>Delete <code>install.php</code> for security!</strong></li>
                        <li>Visit your site and verify everything works</li>
                    </ol>
                </div>
                
                <div class="alert alert-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Security Warning:</strong>
                    Delete this <code>install.php</code> file after installation!
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0"><i class="fas fa-file-code"></i> .env Configuration</h5>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i> Copy to Clipboard
                        </button>
                    </div>
                    <div class="code-block">
                        <pre id="envContent" style="margin: 0; white-space: pre-wrap;"><?= htmlspecialchars(generateEnv($_SESSION['config'] ?? [])) ?></pre>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="hash.php" class="btn btn-primary">
                        <i class="fas fa-key"></i> Set Admin Password
                    </a>
                    <a href="index.php" class="btn btn-success">
                        <i class="fas fa-home"></i> Go to Homepage
                    </a>
                    <a href="?reset" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Start Over
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-secondary">
                <i class="fas fa-code"></i> HytaBansWeb <?= $version ?> &bull; 
                <a href="https://github.com/Yamiru/HytaBansWeb" target="_blank" class="text-secondary">GitHub</a> &bull;
                <a href="https://yamiru.com" target="_blank" class="text-secondary">Yamiru.com</a>
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Test database connection
        document.getElementById('test-connection')?.addEventListener('click', async function() {
            const btn = this;
            const resultDiv = document.getElementById('connection-result');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Testing...';
            
            const formData = new FormData(document.getElementById('db-form'));
            formData.append('ajax_test_db', '1');
            
            try {
                const response = await fetch('install.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                resultDiv.className = result.success ? 'success' : 'error';
                
                let html = '<strong>' + (result.success ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>') + ' ' + result.message + '</strong>';
                
                if (result.success && result.tables) {
                    html += '<br><small class="text-success">Found tables: ' + result.tables.found.join(', ') + '</small>';
                    if (result.tables.missing.length > 0) {
                        html += '<br><small class="text-warning">Missing tables: ' + result.tables.missing.join(', ') + '</small>';
                    }
                }
                
                resultDiv.innerHTML = html;
                resultDiv.style.display = 'block';
            } catch (error) {
                resultDiv.className = 'error';
                resultDiv.innerHTML = '<strong><i class="fas fa-times-circle"></i> Connection test failed</strong>';
                resultDiv.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
        
        // Copy to clipboard
        function copyToClipboard() {
            const content = document.getElementById('envContent').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('Configuration copied to clipboard!');
            }).catch(err => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = content;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Configuration copied to clipboard!');
            });
        }
    </script>
</body>
</html>
