<div class="punishment-detail">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                    <i class="fas fa-home"></i> <?= htmlspecialchars($lang->get('nav.home'), ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= htmlspecialchars(url($type . 's'), ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($lang->get('nav.' . $type . 's'), ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                #<?= htmlspecialchars((string)$punishment['id'], ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </nav>

    <!-- Main Detail Card -->
    <div class="card detail-card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <?php
                    $icon = match($type) {
                        'ban' => 'fa-ban text-danger',
                        'mute' => 'fa-volume-mute text-warning',
                        'warning' => 'fa-exclamation-triangle text-info',
                        'kick' => 'fa-sign-out-alt text-secondary',
                        default => 'fa-list'
                    };
                    ?>
                    <i class="fas <?= $icon ?>"></i>
                    <?= htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8') ?> #<?= htmlspecialchars((string)$punishment['id'], ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <?php if ($punishment['active']): ?>
                    <span class="badge bg-danger">
                        <i class="fas fa-circle"></i> <?= htmlspecialchars($lang->get('status.active'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php elseif ($punishment['removed']): ?>
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($lang->get('status.removed'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary">
                        <i class="fas fa-clock"></i> <?= htmlspecialchars($lang->get('status.expired'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row g-4">
                <!-- Player Info -->
                <div class="col-md-4">
                    <div class="player-card text-center">
                        <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                        <h4 class="mb-1"><?= htmlspecialchars($punishment['name'], ENT_QUOTES, 'UTF-8') ?></h4>
                        <?php if ($controller->shouldShowUuid()): ?>
                        <p class="text-muted small font-monospace"><?= htmlspecialchars($punishment['uuid'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Punishment Details -->
                <div class="col-md-8">
                    <div class="details-grid">
                        <!-- Reason -->
                        <div class="detail-item">
                            <label class="detail-label">
                                <i class="fas fa-comment-alt"></i> <?= htmlspecialchars($lang->get('table.reason'), ENT_QUOTES, 'UTF-8') ?>
                            </label>
                            <div class="detail-value">
                                <?= htmlspecialchars($punishment['reason'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        
                        <!-- Staff -->
                        <div class="detail-item">
                            <label class="detail-label">
                                <i class="fas fa-user-shield"></i> <?= htmlspecialchars($lang->get('table.staff'), ENT_QUOTES, 'UTF-8') ?>
                            </label>
                            <div class="detail-value">
                                <?= htmlspecialchars($punishment['staff'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="detail-item">
                            <label class="detail-label">
                                <i class="fas fa-calendar"></i> <?= htmlspecialchars($lang->get('table.date'), ENT_QUOTES, 'UTF-8') ?>
                            </label>
                            <div class="detail-value">
                                <?= htmlspecialchars($punishment['date'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        
                        <?php if (in_array($type, ['ban', 'mute']) && ($punishment['duration'] || $punishment['until'])): ?>
                            <!-- Duration -->
                            <div class="detail-item">
                                <label class="detail-label">
                                    <i class="fas fa-hourglass-half"></i> <?= htmlspecialchars($lang->get('detail.duration'), ENT_QUOTES, 'UTF-8') ?>
                                </label>
                                <div class="detail-value">
                                    <?php if ($punishment['duration']): ?>
                                        <span class="badge <?= $punishment['until_timestamp'] == 0 ? 'bg-danger' : 'bg-warning' ?>">
                                            <?= htmlspecialchars($punishment['duration'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <?= htmlspecialchars($lang->get('punishment.permanent'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Time Left -->
                            <?php if ($punishment['active'] && $punishment['timeLeft']): ?>
                                <div class="detail-item">
                                    <label class="detail-label">
                                        <i class="fas fa-clock"></i> <?= htmlspecialchars($lang->get('detail.time_left'), ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                    <div class="detail-value">
                                        <span class="badge bg-warning">
                                            <?= htmlspecialchars($punishment['timeLeft'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($punishment['removed']): ?>
                            <!-- Removed Info -->
                            <div class="detail-item full-width">
                                <label class="detail-label">
                                    <i class="fas fa-undo"></i> <?= htmlspecialchars($lang->get('detail.removed_info'), ENT_QUOTES, 'UTF-8') ?>
                                </label>
                                <div class="detail-value">
                                    <span class="text-success">
                                        <?= htmlspecialchars($lang->get('detail.removed_by'), ENT_QUOTES, 'UTF-8') ?>: 
                                        <?= htmlspecialchars($punishment['removed_by'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php if ($punishment['removed_at']): ?>
                                        <small class="text-muted d-block">
                                            <?= htmlspecialchars($punishment['removed_at'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Punishments -->
    <?php if (!empty($relatedPunishments)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history"></i> <?= htmlspecialchars($lang->get('detail.other_punishments'), ENT_QUOTES, 'UTF-8') ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?= htmlspecialchars($lang->get('table.type'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang->get('table.reason'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang->get('table.staff'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang->get('table.date'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang->get('table.status'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang->get('table.actions'), ENT_QUOTES, 'UTF-8') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatedPunishments as $related): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $typeClass = match($related['type']) {
                                            'bans' => 'bg-danger',
                                            'mutes' => 'bg-warning',
                                            'warnings' => 'bg-info',
                                            'kicks' => 'bg-secondary',
                                            default => 'bg-dark'
                                        };
                                        ?>
                                        <span class="badge <?= $typeClass ?>">
                                            <?= htmlspecialchars(ucfirst(rtrim($related['type'], 's')), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($related['reason'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($related['reason'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td><?= htmlspecialchars($related['staff'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($related['date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($related['active']): ?>
                                            <span class="status-badge status-active"><?= htmlspecialchars($lang->get('status.active'), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive"><?= htmlspecialchars($lang->get('status.inactive'), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars(url('detail?type=' . rtrim($related['type'], 's') . '&id=' . $related['id']), ENT_QUOTES, 'UTF-8') ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.punishment-detail {
    animation: fadeIn 0.3s ease-out;
}

.detail-card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.player-card {
    padding: 2rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.detail-item {
    background: var(--bg-secondary);
    padding: 1rem;
    border-radius: var(--radius-md);
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.detail-value {
    color: var(--text-primary);
    font-weight: 600;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
