<!-- Hero Search Section -->
<div class="hero-search">
    <div class="hero-search-content">
        <h1 class="hero-title">
            <span><?= htmlspecialchars($config['site_name'] ?? 'Server', ENT_QUOTES, 'UTF-8') ?></span> <?= htmlspecialchars($lang->get('home.welcome'), ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="hero-subtitle"><?= htmlspecialchars($lang->get('home.description'), ENT_QUOTES, 'UTF-8') ?></p>
        
        <div class="search-box">
            <form id="search-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="search-input-wrapper">
                    <input type="text" id="search-input" class="form-control" placeholder="<?= htmlspecialchars($lang->get('search.placeholder'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" maxlength="36">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?= htmlspecialchars($lang->get('search.button'), ENT_QUOTES, 'UTF-8') ?></button>
                </div>
            </form>
        </div>
        <div id="search-results"></div>
    </div>
</div>

<?php if (isset($searchQuery) && !empty($searchQuery)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const si = document.getElementById('search-input'), sf = document.getElementById('search-form');
    if (si && sf) { si.value = <?= json_encode($searchQuery) ?>; setTimeout(() => sf.dispatchEvent(new Event('submit')), 100); }
});
</script>
<?php endif; ?>

<!-- Stats Bento Grid -->
<div class="stats-bento">
    <div class="stat-card bans">
        <div class="stat-header">
            <div class="stat-icon"><i class="fas fa-ban"></i></div>
            <span class="stat-badge active"><?= number_format($stats['bans_active'] ?? 0) ?> active</span>
        </div>
        <div class="stat-value"><?= number_format($stats['bans'] ?? 0) ?></div>
        <div class="stat-label"><?= htmlspecialchars($lang->get('nav.bans'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="stat-footer">
            <span class="stat-total"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="stat-card mutes">
        <div class="stat-header">
            <div class="stat-icon"><i class="fas fa-volume-mute"></i></div>
            <span class="stat-badge active"><?= number_format($stats['mutes_active'] ?? 0) ?> active</span>
        </div>
        <div class="stat-value"><?= number_format($stats['mutes'] ?? 0) ?></div>
        <div class="stat-label"><?= htmlspecialchars($lang->get('nav.mutes'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="stat-footer">
            <span class="stat-total"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="stat-card warnings">
        <div class="stat-header"><div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div></div>
        <div class="stat-value"><?= number_format($stats['warnings'] ?? 0) ?></div>
        <div class="stat-label"><?= htmlspecialchars($lang->get('nav.warnings'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="stat-footer">
            <span class="stat-total"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= htmlspecialchars(url('warnings'), ENT_QUOTES, 'UTF-8') ?>" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="stat-card kicks">
        <div class="stat-header"><div class="stat-icon"><i class="fas fa-door-open"></i></div></div>
        <div class="stat-value"><?= number_format($stats['kicks'] ?? 0) ?></div>
        <div class="stat-label"><?= htmlspecialchars($lang->get('nav.kicks'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="stat-footer">
            <span class="stat-total"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= htmlspecialchars(url('kicks'), ENT_QUOTES, 'UTF-8') ?>" class="stat-link">View all <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="activity-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-clock"></i> <?= htmlspecialchars($lang->get('home.recent_activity'), ENT_QUOTES, 'UTF-8') ?></h2>
        <a href="<?= htmlspecialchars(url('stats'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-line"></i> <?= htmlspecialchars($lang->get('stats.title'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <div class="activity-grid">
        <div class="activity-card">
            <div class="activity-card-header">
                <h3 class="activity-card-title bans"><i class="fas fa-ban"></i> <?= htmlspecialchars($lang->get('home.recent_bans'), ENT_QUOTES, 'UTF-8') ?></h3>
                <span class="badge bg-danger"><?= count($recentBans) ?></span>
            </div>
            <div class="activity-card-body">
                <?php if (empty($recentBans)): ?>
                    <div class="activity-empty"><div class="activity-empty-icon"><i class="fas fa-check-circle"></i></div><p class="activity-empty-text"><?= htmlspecialchars($lang->get('home.no_recent_bans'), ENT_QUOTES, 'UTF-8') ?></p></div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentBans as $ban): $pn = $ban['player_name'] ?? $ban['name'] ?? 'Unknown'; ?>
                            <div class="activity-item clickable-row" data-href="<?= htmlspecialchars(url('detail?type=ban&id=' . $ban['id']), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="activity-avatar"><i class="fas fa-user"></i></div>
                                <div class="activity-info">
                                    <div class="activity-name"><?= htmlspecialchars($pn, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="activity-reason"><?php $r=$ban['reason']??'No reason';echo htmlspecialchars(mb_substr($r,0,35).(mb_strlen($r)>35?'...':''),ENT_QUOTES,'UTF-8');?></div>
                                </div>
                                <div class="activity-meta">
                                    <div class="activity-time"><?= htmlspecialchars($controller->formatDate((int)$ban['time']), ENT_QUOTES, 'UTF-8') ?></div>
                                    <span class="activity-status <?= ($ban['active'] ?? false) ? 'active' : 'inactive' ?>"><?= ($ban['active'] ?? false) ? 'Active' : 'Expired' ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($recentBans)): ?><div class="activity-card-footer"><a href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-danger btn-sm"><?= htmlspecialchars($lang->get('home.view_all_bans'), ENT_QUOTES, 'UTF-8') ?></a></div><?php endif; ?>
        </div>
        <div class="activity-card">
            <div class="activity-card-header">
                <h3 class="activity-card-title mutes"><i class="fas fa-volume-mute"></i> <?= htmlspecialchars($lang->get('home.recent_mutes'), ENT_QUOTES, 'UTF-8') ?></h3>
                <span class="badge bg-warning"><?= count($recentMutes) ?></span>
            </div>
            <div class="activity-card-body">
                <?php if (empty($recentMutes)): ?>
                    <div class="activity-empty"><div class="activity-empty-icon"><i class="fas fa-volume-up"></i></div><p class="activity-empty-text"><?= htmlspecialchars($lang->get('home.no_recent_mutes'), ENT_QUOTES, 'UTF-8') ?></p></div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentMutes as $mute): $pn = $mute['player_name'] ?? $mute['name'] ?? 'Unknown'; ?>
                            <div class="activity-item clickable-row" data-href="<?= htmlspecialchars(url('detail?type=mute&id=' . $mute['id']), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="activity-avatar"><i class="fas fa-user"></i></div>
                                <div class="activity-info">
                                    <div class="activity-name"><?= htmlspecialchars($pn, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="activity-reason"><?php $r=$mute['reason']??'No reason';echo htmlspecialchars(mb_substr($r,0,35).(mb_strlen($r)>35?'...':''),ENT_QUOTES,'UTF-8');?></div>
                                </div>
                                <div class="activity-meta">
                                    <div class="activity-time"><?= htmlspecialchars($controller->formatDate((int)$mute['time']), ENT_QUOTES, 'UTF-8') ?></div>
                                    <span class="activity-status <?= ($mute['active'] ?? false) ? 'active' : 'inactive' ?>"><?= ($mute['active'] ?? false) ? 'Active' : 'Expired' ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($recentMutes)): ?><div class="activity-card-footer"><a href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-warning btn-sm"><?= htmlspecialchars($lang->get('home.view_all_mutes'), ENT_QUOTES, 'UTF-8') ?></a></div><?php endif; ?>
        </div>
    </div>
</div>
