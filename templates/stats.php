<div class="stats-page">
    <!-- Header -->
    <div class="stats-header">
        <div class="stats-header-content">
            <h1 class="stats-title">
                <span class="stats-title-icon">
                    <i class="fas fa-chart-bar"></i>
                </span>
                <?= htmlspecialchars($lang->get('nav.statistics'), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="stats-subtitle"><?= htmlspecialchars($lang->get('stats.title'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <button id="clear-cache-btn" class="btn btn-tech btn-sm">
            <i class="fas fa-sync-alt"></i>
            <span><?= htmlspecialchars($lang->get('stats.clear_cache'), ENT_QUOTES, 'UTF-8') ?></span>
        </button>
    </div>

    <!-- Stats Grid - Tech Style -->
    <div class="stats-grid-tech">
        <div class="stat-card-tech stat-bans">
            <div class="stat-card-glow"></div>
            <div class="stat-card-content">
                <div class="stat-icon-tech">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="stat-data">
                    <div class="stat-number-tech" data-count="<?= $stats['bans_active'] ?? 0 ?>">
                        <?= number_format($stats['bans_active'] ?? 0) ?>
                    </div>
                    <div class="stat-label-tech"><?= htmlspecialchars($lang->get('stats.active_bans'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill" style="width: <?= min(100, (($stats['bans_active'] ?? 0) / max(1, $stats['bans'] ?? 1)) * 100) ?>%"></div>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-total-label"><?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="stat-total-value"><?= number_format($stats['bans'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stat-card-tech stat-mutes">
            <div class="stat-card-glow"></div>
            <div class="stat-card-content">
                <div class="stat-icon-tech">
                    <i class="fas fa-volume-mute"></i>
                </div>
                <div class="stat-data">
                    <div class="stat-number-tech" data-count="<?= $stats['mutes_active'] ?? 0 ?>">
                        <?= number_format($stats['mutes_active'] ?? 0) ?>
                    </div>
                    <div class="stat-label-tech"><?= htmlspecialchars($lang->get('stats.active_mutes'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill" style="width: <?= min(100, (($stats['mutes_active'] ?? 0) / max(1, $stats['mutes'] ?? 1)) * 100) ?>%"></div>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-total-label"><?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="stat-total-value"><?= number_format($stats['mutes'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stat-card-tech stat-warnings">
            <div class="stat-card-glow"></div>
            <div class="stat-card-content">
                <div class="stat-icon-tech">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-data">
                    <div class="stat-number-tech" data-count="<?= $stats['warnings'] ?? 0 ?>">
                        <?= number_format($stats['warnings'] ?? 0) ?>
                    </div>
                    <div class="stat-label-tech"><?= htmlspecialchars($lang->get('stats.total_warnings'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill" style="width: 100%"></div>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-total-label"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stat-card-tech stat-kicks">
            <div class="stat-card-glow"></div>
            <div class="stat-card-content">
                <div class="stat-icon-tech">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="stat-data">
                    <div class="stat-number-tech" data-count="<?= $stats['kicks'] ?? 0 ?>">
                        <?= number_format($stats['kicks'] ?? 0) ?>
                    </div>
                    <div class="stat-label-tech"><?= htmlspecialchars($lang->get('stats.total_kicks'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill" style="width: 100%"></div>
                    </div>
                    <div class="stat-meta">
                        <span class="stat-total-label"><?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline -->
    <?php if (!empty($stats['recent_activity'])): ?>
    <div class="stats-section">
        <div class="section-header-tech">
            <div class="section-title-wrapper">
                <span class="section-icon"><i class="fas fa-clock"></i></span>
                <h2 class="section-title"><?= htmlspecialchars($lang->get('stats.recent_activity_overview'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
        </div>
        
        <div class="activity-grid-tech">
            <!-- Last 24 Hours -->
            <div class="activity-card-tech">
                <div class="activity-card-header">
                    <span class="activity-period-badge"><?= htmlspecialchars($lang->get('stats.last_24h'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="activity-card-body">
                    <div class="activity-item-tech">
                        <span class="activity-dot bans"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_24h']['bans'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot mutes"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_24h']['mutes'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot warnings"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_24h']['warnings'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot kicks"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_24h']['kicks'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Last 7 Days -->
            <div class="activity-card-tech">
                <div class="activity-card-header">
                    <span class="activity-period-badge"><?= htmlspecialchars($lang->get('stats.last_7d'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="activity-card-body">
                    <div class="activity-item-tech">
                        <span class="activity-dot bans"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_7d']['bans'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot mutes"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_7d']['mutes'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot warnings"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_7d']['warnings'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot kicks"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_7d']['kicks'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Last 30 Days -->
            <div class="activity-card-tech">
                <div class="activity-card-header">
                    <span class="activity-period-badge"><?= htmlspecialchars($lang->get('stats.last_30d'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="activity-card-body">
                    <div class="activity-item-tech">
                        <span class="activity-dot bans"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_30d']['bans'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot mutes"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_30d']['mutes'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot warnings"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_30d']['warnings'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="activity-item-tech">
                        <span class="activity-dot kicks"></span>
                        <span class="activity-count"><?= $stats['recent_activity']['last_30d']['kicks'] ?? 0 ?></span>
                        <span class="activity-type"><?= htmlspecialchars($lang->get('stats.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Players & Staff Section -->
    <div class="stats-two-column">
        <!-- Most Banned Players -->
        <?php if (!empty($stats['most_banned_players'])): ?>
        <div class="stats-section">
            <div class="section-header-tech">
                <div class="section-title-wrapper">
                    <span class="section-icon text-danger"><i class="fas fa-user-slash"></i></span>
                    <h2 class="section-title"><?= htmlspecialchars($lang->get('stats.most_banned_players'), ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
            
            <div class="leaderboard-tech">
                <?php $rank = 1; ?>
                <?php foreach ($stats['most_banned_players'] as $player): ?>
                <div class="leaderboard-item">
                    <div class="leaderboard-rank rank-<?= $rank ?>"><?= $rank ?></div>
                    <div class="leaderboard-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="leaderboard-info">
                        <span class="leaderboard-name"><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="leaderboard-stats">
                        <span class="badge badge-danger"><?= $player['ban_count'] ?> <?= htmlspecialchars($lang->get('stats.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <?php $rank++; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Most Active Staff -->
        <?php if (!empty($stats['most_active_staff'])): ?>
        <div class="stats-section">
            <div class="section-header-tech">
                <div class="section-title-wrapper">
                    <span class="section-icon text-success"><i class="fas fa-user-shield"></i></span>
                    <h2 class="section-title"><?= htmlspecialchars($lang->get('stats.most_active_staff'), ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
            
            <div class="leaderboard-tech">
                <?php $rank = 1; ?>
                <?php foreach ($stats['most_active_staff'] as $staff): ?>
                <div class="leaderboard-item">
                    <div class="leaderboard-rank rank-<?= $rank ?>"><?= $rank ?></div>
                    <div class="leaderboard-avatar staff">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="leaderboard-info">
                        <span class="leaderboard-name"><?= htmlspecialchars($staff['staff_name'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="leaderboard-stats">
                        <span class="badge badge-primary"><?= $staff['total_punishments'] ?> total</span>
                        <div class="mini-stats">
                            <?php if (($staff['bans'] ?? 0) > 0): ?>
                                <span class="mini-stat bans"><?= $staff['bans'] ?>B</span>
                            <?php endif; ?>
                            <?php if (($staff['mutes'] ?? 0) > 0): ?>
                                <span class="mini-stat mutes"><?= $staff['mutes'] ?>M</span>
                            <?php endif; ?>
                            <?php if (($staff['warnings'] ?? 0) > 0): ?>
                                <span class="mini-stat warnings"><?= $staff['warnings'] ?>W</span>
                            <?php endif; ?>
                            <?php if (($staff['kicks'] ?? 0) > 0): ?>
                                <span class="mini-stat kicks"><?= $staff['kicks'] ?>K</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php $rank++; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top Ban Reasons -->
    <?php if (!empty($stats['top_ban_reasons'])): ?>
    <div class="stats-section">
        <div class="section-header-tech">
            <div class="section-title-wrapper">
                <span class="section-icon text-info"><i class="fas fa-list-alt"></i></span>
                <h2 class="section-title"><?= htmlspecialchars($lang->get('stats.top_ban_reasons'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
        </div>
        
        <div class="reasons-grid-tech">
            <?php foreach ($stats['top_ban_reasons'] as $reason): ?>
            <div class="reason-card-tech">
                <div class="reason-text">
                    <?= htmlspecialchars(mb_substr($reason['reason'], 0, 60), ENT_QUOTES, 'UTF-8') ?>
                    <?php if (mb_strlen($reason['reason']) > 60): ?>...<?php endif; ?>
                </div>
                <div class="reason-count">
                    <span class="count-value"><?= $reason['count'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daily Activity Chart -->
    <?php if (!empty($stats['daily_activity'])): ?>
    <div class="stats-section">
        <div class="section-header-tech">
            <div class="section-title-wrapper">
                <span class="section-icon text-warning"><i class="fas fa-calendar-alt"></i></span>
                <h2 class="section-title"><?= htmlspecialchars($lang->get('stats.activity_by_day'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
        </div>
        
        <div class="chart-container-tech">
            <div class="bar-chart-tech">
                <?php 
                $maxCount = max(array_column($stats['daily_activity'], 'count'));
                foreach ($stats['daily_activity'] as $day): 
                    $percentage = $maxCount > 0 ? ($day['count'] / $maxCount) * 100 : 0;
                ?>
                <div class="bar-column">
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="height: <?= min(100, $percentage) ?>%">
                            <span class="bar-value"><?= $day['count'] ?></span>
                        </div>
                    </div>
                    <div class="bar-label"><?= substr($day['day_name'], 0, 3) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Clear Cache Modal -->
    <div class="modal fade" id="cacheModal" tabindex="-1" aria-labelledby="cacheModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-tech">
                <div class="modal-header">
                    <h5 class="modal-title" id="cacheModalLabel">
                        <i class="fas fa-sync-alt text-primary me-2"></i>
                        <?= htmlspecialchars($lang->get('stats.clear_cache'), ENT_QUOTES, 'UTF-8') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-icon-wrapper">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="modal-text"><?= htmlspecialchars($lang->get('stats.cache_clear_confirm') ?? 'This will clear all cached statistics data and force a refresh.', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" id="confirm-clear-cache" class="btn btn-danger">
                        <i class="fas fa-sync-alt me-1"></i> <?= htmlspecialchars($lang->get('stats.clear_cache'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
