<?php
/**
 * ============================================================================
 *  HytaBansWeb - Demo & Video System Installer
 * ============================================================================
 *
 *  Plugin Name:   HytaBansWeb
 *  Description:   Demo & Video Management System for HytaBansWeb
 *  Version:       1.0
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 * ============================================================================
 */

session_start();

$version = '1.0';
$installReady = false;
$messages = [];
$errors = [];

// Check if we're in the correct location
$requiredFiles = ['index.php', 'config/app.php', 'controllers/AdminController.php'];
$inCorrectLocation = true;

foreach ($requiredFiles as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $inCorrectLocation = false;
        break;
    }
}

// Handle installation
if (isset($_POST['install']) && $inCorrectLocation) {
    $installReady = true;
    
    // Create directories
    $dirs = ['demos' => 0755, 'demos/data' => 0777, 'demos/uploads' => 0777];

    foreach ($dirs as $dir => $perms) {
        $fullPath = __DIR__ . '/' . $dir;
        if (!file_exists($fullPath)) {
            if (@mkdir($fullPath, $perms, true)) {
                $messages[] = "✓ Vytvorený adresár: /{$dir}/";
                @chmod($fullPath, $perms);
            } else {
                $errors[] = "✗ Nepodarilo sa vytvoriť: /{$dir}/";
            }
        } else {
            $messages[] = "✓ Adresár existuje: /{$dir}/";
        }
    }

    // Create main demos/index.php
    $demoIndexContent = '<?php
session_start();

if (!isset($_SESSION["admin_authenticated"]) || $_SESSION["admin_authenticated"] !== true) {
    header("Location: ../admin");
    exit();
}

function loadEnvFile($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), "#") === 0 || strpos($line, "=") === false) continue;
        list($name, $value) = explode("=", $line, 2);
        putenv(sprintf("%s=%s", trim($name), trim($value)));
    }
    return true;
}

loadEnvFile(dirname(__DIR__) . "/.env");

$pdo = null;
$activeBans = [];
$dbConnected = false;
$table_prefix = getenv("TABLE_PREFIX") ?: "hb_";

try {
    $pdo = new PDO(
        "mysql:host=" . (getenv("DB_HOST") ?: "localhost") . ";port=" . (getenv("DB_PORT") ?: "3306") . ";dbname=" . getenv("DB_NAME") . ";charset=utf8mb4",
        getenv("DB_USER"),
        getenv("DB_PASS") ?: "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $dbConnected = true;
    $stmt = $pdo->query("SELECT id, uuid, reason, time FROM {$table_prefix}bans WHERE active = 1 ORDER BY time DESC LIMIT 100");
    $activeBans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dbError = $e->getMessage();
}

$dataFile = __DIR__ . "/data/assignments.json";
$uploadsDir = __DIR__ . "/uploads/";
$assignments = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) ?: [] : [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["demoFile"])) {
    $file = $_FILES["demoFile"];
    $banId = intval($_POST["banId"] ?? 0);
    if ($file["error"] === UPLOAD_ERR_OK && $banId > 0) {
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (in_array($ext, ["dem", "mp4", "webm", "mkv", "avi", "mov"])) {
            $newFileName = $banId . "." . $ext;
            if (move_uploaded_file($file["tmp_name"], $uploadsDir . $newFileName)) {
                $assignments[$banId] = ["file" => $newFileName, "uploaded" => date("Y-m-d H:i:s"), "size" => filesize($uploadsDir . $newFileName)];
                file_put_contents($dataFile, json_encode($assignments, JSON_PRETTY_PRINT));
                $successMessage = "Súbor úspešne nahraný pre Ban #{$banId}";
            }
        }
    }
}

if (isset($_POST["deleteFile"])) {
    $banId = intval($_POST["deleteFile"]);
    if (isset($assignments[$banId])) {
        @unlink($uploadsDir . $assignments[$banId]["file"]);
        unset($assignments[$banId]);
        file_put_contents($dataFile, json_encode($assignments, JSON_PRETTY_PRINT));
        $successMessage = "Súbor pre Ban #{$banId} bol odstránený";
    }
}

function getPlayerName($pdo, $uuid, $prefix) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM {$prefix}history WHERE uuid = ? ORDER BY date DESC LIMIT 1");
        $stmt->execute([$uuid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["name"] ?? "Unknown";
    } catch (Exception $e) { return "Unknown"; }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Management - HytaBansWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --bg-dark: #09090b; --bg-card: #18181b; --bg-input: #27272a; --primary: #7c3aed; --success: #22c55e; --danger: #ef4444; --text-primary: #fafafa; --text-secondary: #a1a1aa; --border: #3f3f46; }
        body { background: var(--bg-dark); color: var(--text-primary); font-family: Inter, sans-serif; min-height: 100vh; }
        .container { max-width: 1200px; padding: 2rem 1rem; }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1rem; }
        .card-header { background: rgba(124, 58, 237, 0.15); border-bottom: 1px solid var(--border); padding: 1.5rem; }
        .card-header h5 { color: var(--text-primary); margin: 0; }
        .table { color: var(--text-primary); }
        .table th { color: var(--text-secondary); border-color: var(--border); }
        .table td { border-color: var(--border); vertical-align: middle; color: var(--text-primary); }
        .table-hover tbody tr:hover { background: rgba(124, 58, 237, 0.1); }
        .btn-primary { background: linear-gradient(135deg, var(--primary), #6d28d9); border: none; }
        .form-control, .form-select { background: var(--bg-input); border: 1px solid var(--border); color: var(--text-primary); }
        .form-control:focus, .form-select:focus { background: var(--bg-input); border-color: var(--primary); color: var(--text-primary); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2); }
        .back-link { color: var(--text-secondary); text-decoration: none; }
        .back-link:hover { color: var(--primary); }
        .text-muted { color: var(--text-secondary) !important; }
        .text-center p { color: var(--text-secondary); }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid var(--success); color: #86efac; }
        .badge { font-weight: 500; }
        .status-connected { color: var(--success); }
        .status-error { color: var(--danger); }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="../admin" class="back-link"><i class="fas fa-arrow-left"></i> Späť do administrácie</a>
                <h1 class="mt-2"><i class="fas fa-video"></i> Demo Management</h1>
            </div>
            <div>
                <?php if ($dbConnected): ?>
                <span class="status-connected"><i class="fas fa-check-circle"></i> DB pripojená</span>
                <?php else: ?>
                <span class="status-error"><i class="fas fa-times-circle"></i> DB chyba</span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-upload"></i> Nahrať Demo/Video</h5></div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Ban ID</label>
                                <select class="form-select" name="banId" required>
                                    <option value="">-- Vyber ban --</option>
                                    <?php foreach ($activeBans as $ban): ?>
                                    <option value="<?= $ban["id"] ?>">#<?= $ban["id"] ?> - <?= htmlspecialchars(getPlayerName($pdo, $ban["uuid"], $table_prefix)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Súbor</label>
                                <input type="file" class="form-control" name="demoFile" accept=".dem,.mp4,.webm,.mkv,.avi,.mov" required>
                                <small class="text-muted">Povolené: .dem, .mp4, .webm, .mkv, .avi, .mov</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-upload"></i> Nahrať</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-file-video"></i> Priradené súbory (<?= count($assignments) ?>)</h5></div>
                    <div class="card-body p-0">
                        <?php if (empty($assignments)): ?>
                        <div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3"></i><p>Žiadne priradené súbory</p></div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead><tr><th>Ban ID</th><th>Súbor</th><th>Veľkosť</th><th>Nahraný</th><th>Akcie</th></tr></thead>
                                <tbody>
                                    <?php foreach ($assignments as $banId => $info): ?>
                                    <tr>
                                        <td><span class="badge bg-primary">#<?= $banId ?></span></td>
                                        <td><?= htmlspecialchars($info["file"]) ?></td>
                                        <td><?= number_format(($info["size"] ?? 0) / 1024 / 1024, 2) ?> MB</td>
                                        <td><?= htmlspecialchars($info["uploaded"] ?? "N/A") ?></td>
                                        <td>
                                            <a href="uploads/<?= htmlspecialchars($info["file"]) ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-download"></i></a>
                                            <form method="POST" style="display: inline;"><input type="hidden" name="deleteFile" value="<?= $banId ?>"><button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Naozaj odstrániť?\')"><i class="fas fa-trash"></i></button></form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-ban"></i> Aktívne bany (<?= count($activeBans) ?>)</h5></div>
            <div class="card-body p-0">
                <?php if (empty($activeBans)): ?>
                <div class="text-center text-muted py-5"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Žiadne aktívne bany</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>ID</th><th>Hráč</th><th>Dôvod</th><th>Čas</th><th>Demo</th></tr></thead>
                        <tbody>
                            <?php foreach ($activeBans as $ban): ?>
                            <tr>
                                <td><span class="badge bg-danger">#<?= $ban["id"] ?></span></td>
                                <td><?= htmlspecialchars(getPlayerName($pdo, $ban["uuid"], $table_prefix)) ?></td>
                                <td><?= htmlspecialchars(substr($ban["reason"] ?? "", 0, 50)) ?>...</td>
                                <td><?= date("d.m.Y H:i", intval($ban["time"] / 1000)) ?></td>
                                <td><?php if (isset($assignments[$ban["id"]])): ?><span class="badge bg-success"><i class="fas fa-check"></i> Priradené</span><?php else: ?><span class="badge bg-secondary"><i class="fas fa-minus"></i> Chýba</span><?php endif; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted"><small>HytaBansWeb Demo System | <a href="https://yamiru.com" target="_blank" class="text-muted">Yamiru.com</a></small></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';

    if (file_put_contents(__DIR__ . '/demos/index.php', $demoIndexContent)) {
        $messages[] = "✓ Vytvorený súbor: /demos/index.php";
    } else {
        $errors[] = "✗ Nepodarilo sa vytvoriť: /demos/index.php";
    }

    // Create .htaccess files
    file_put_contents(__DIR__ . '/demos/uploads/.htaccess', "Options -Indexes\n<Files \"*.json\">\nDeny from all\n</Files>");
    file_put_contents(__DIR__ . '/demos/data/.htaccess', "Order Allow,Deny\nDeny from all");
    file_put_contents(__DIR__ . '/demos/data/assignments.json', '{}');
    $messages[] = "✓ Vytvorené .htaccess a assignments.json";
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HytaBansWeb - Demo System Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --bg-dark: #09090b; --bg-card: #18181b; --bg-input: #27272a; --primary: #7c3aed; --success: #22c55e; --danger: #ef4444; --warning: #f59e0b; --text-primary: #fafafa; --text-secondary: #a1a1aa; --border: #3f3f46; }
        body { background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%); color: var(--text-primary); font-family: Inter, sans-serif; min-height: 100vh; padding: 2rem; }
        .container { max-width: 800px; }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 1rem; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .card-header { background: rgba(124, 58, 237, 0.15); border-bottom: 1px solid var(--border); padding: 1.5rem; }
        .card-header h2 { margin: 0; background: linear-gradient(135deg, var(--primary), #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-body { padding: 2rem; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), #6d28d9); border: none; padding: 0.75rem 2rem; font-weight: 600; border-radius: 0.5rem; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3); }
        .message { padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 0.5rem; color: var(--text-primary); }
        .message.success { background: rgba(34, 197, 94, 0.15); border-left: 3px solid var(--success); color: #86efac; }
        .message.error { background: rgba(239, 68, 68, 0.15); border-left: 3px solid var(--danger); color: #fca5a5; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
        .feature { background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 0.5rem; text-align: center; }
        .feature i { font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem; }
        .feature h6 { margin-bottom: 0.5rem; color: var(--text-primary); }
        .feature p { font-size: 0.875rem; color: var(--text-secondary); margin: 0; }
        .alert-warning { background: rgba(245, 158, 11, 0.15); border: 1px solid var(--warning); color: #fde047; }
        .alert-warning h6 { color: #fde047; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid var(--success); color: #86efac; }
        .alert-success h5 { color: #4ade80; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: #fca5a5; }
        .alert-danger h5 { color: #f87171; }
        .status-ok { color: var(--success); }
        code { background: rgba(124, 58, 237, 0.2); padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #c4b5fd; }
        .text-muted { color: var(--text-secondary) !important; }
        h4, h5 { color: var(--text-primary); }
        p { color: var(--text-secondary); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2><i class="fas fa-video"></i> HytaBansWeb Demo System</h2>
                <p class="text-muted mb-0">Version <?= $version ?></p>
            </div>
            <div class="card-body">
                <?php if (!$inCorrectLocation): ?>
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Chyba umiestnenia</h5>
                    <p>Tento inštalátor musí byť umiestnený v koreňovom adresári HytaBansWeb!</p>
                </div>
                
                <?php elseif (!$installReady): ?>
                <div class="text-center mb-4">
                    <span class="status-ok"><i class="fas fa-check-circle fa-2x"></i></span>
                    <h4 class="mt-2">Pripravené na inštaláciu</h4>
                    <p class="text-muted">HytaBansWeb inštalácia bola detekovaná</p>
                </div>
                
                <div class="feature-grid">
                    <div class="feature"><i class="fas fa-database"></i><h6>Auto DB Connect</h6><p>Automatické pripojenie z .env</p></div>
                    <div class="feature"><i class="fas fa-hashtag"></i><h6>Ban ID System</h6><p>Jednoduché priradenie podľa ID</p></div>
                    <div class="feature"><i class="fas fa-film"></i><h6>Multi-Format</h6><p>Demo a video formáty</p></div>
                    <div class="feature"><i class="fas fa-shield-alt"></i><h6>Bezpečné</h6><p>Chránené adresáre</p></div>
                </div>
                
                <form method="POST" class="text-center">
                    <button type="submit" name="install" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i> Inštalovať Demo System
                    </button>
                </form>
                
                <?php else: ?>
                <h5 class="mb-3">Výsledky inštalácie:</h5>
                <?php foreach ($messages as $msg): ?>
                <div class="message success"><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
                <?php foreach ($errors as $err): ?>
                <div class="message error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
                
                <?php if (empty($errors)): ?>
                <div class="alert alert-success mt-4">
                    <h5><i class="fas fa-check-circle"></i> Inštalácia dokončená!</h5>
                    <p class="mb-0">Demo systém bol úspešne nainštalovaný.</p>
                </div>
                <div class="text-center mt-4">
                    <a href="demos/" class="btn btn-primary btn-lg"><i class="fas fa-external-link-alt"></i> Otvoriť Demo System</a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <div class="alert alert-warning mt-4">
                    <h6><i class="fas fa-exclamation-triangle"></i> Bezpečnostné upozornenie</h6>
                    <p class="mb-0"><strong>Odstráňte tento súbor <code>install-demos.php</code></strong> po dokončení inštalácie!</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 text-muted">
            <small><a href="https://github.com/Yamiru/HytaBansWeb" target="_blank" class="text-muted">GitHub</a> &bull; <a href="https://yamiru.com" target="_blank" class="text-muted">Yamiru.com</a></small>
        </div>
    </div>
</body>
</html>
