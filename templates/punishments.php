<div class="punishments-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <?php
        $icon = match($type) {
            'bans' => 'fa-ban text-danger',
            'mutes' => 'fa-volume-mute text-warning', 
            'warnings' => 'fa-exclamation-triangle text-info',
            'kicks' => 'fa-sign-out-alt text-secondary',
            default => 'fa-list'
        };
        ?>
        <i class="fas <?= $icon ?>"></i>
        <?= $title ?>
    </h1>
</div>

<?php if (empty($punishments)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted"><?= $lang->get('punishments.no_data') ?></h4>
            <p class="text-muted"><?= $lang->get('punishments.no_data_desc') ?></p>
        </div>
    </div>
<?php else: ?>
    <!-- Desktop Table -->
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><a href="?sort=name&order=<?= ($sortParams['sort'] === 'name' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.player') ?> <i class="fas fa-sort"></i></a></th>
                    <th><a href="?sort=id&order=<?= ($sortParams['sort'] === 'id' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link">ID <i class="fas fa-sort"></i></a></th>
                    <th><a href="?sort=reason&order=<?= ($sortParams['sort'] === 'reason' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.reason') ?> <i class="fas fa-sort"></i></a></th>
                    <th><a href="?sort=banned_by_name&order=<?= ($sortParams['sort'] === 'banned_by_name' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.staff') ?> <i class="fas fa-sort"></i></a></th>
                    <th><a href="?sort=time&order=<?= ($sortParams['sort'] === 'time' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.date') ?> <i class="fas fa-sort"></i></a></th>
                    <?php if ($type !== 'kicks'): ?>
                        <th><a href="?sort=until&order=<?= ($sortParams['sort'] === 'until' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.expires') ?> <i class="fas fa-sort"></i></a></th>
                    <?php endif; ?>
                    <th><a href="?sort=active&order=<?= ($sortParams['sort'] === 'active' && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC' ?>" class="sort-link"><?= $lang->get('table.status') ?> <i class="fas fa-sort"></i></a></th>
                    <th><?= $lang->get('table.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($punishments as $punishment): ?>
                    <tr>
                        <td>
                            <div class="player-info">
                                <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                                <div>
                                    <div class="fw-bold"><?= $punishment['name'] ?></div>
                                    <?php if ($controller->shouldShowUuid()): ?>
                                    <small class="text-muted font-monospace">
                                        <?= substr($punishment['uuid'], 0, 36) ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="font-monospace text-muted">
                                <?= $punishment['id'] ?>
                            </small>
                        </td>
                        <td>
                            <span class="reason-cell" 
                                  title="<?= htmlspecialchars($punishment['reason'], ENT_QUOTES, 'UTF-8') ?>"
                                  data-bs-toggle="tooltip" 
                                  data-bs-title="<?= htmlspecialchars($punishment['reason'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php 
                                $reason = $punishment['reason'];
                                if (strlen($reason) > 15) {
                                    echo htmlspecialchars(substr($reason, 0, 15), ENT_QUOTES, 'UTF-8') . '...';
                                } else {
                                    echo htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
                                }
                                ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-primary"><?= $punishment['staff'] ?></span>
                        </td>
                        <td>
                            <small><?= $punishment['date'] ?></small>
                        </td>
                        <?php if ($type !== 'kicks'): ?>
                            <td class="text-center">
                                <?php if ($punishment['removed_by']): ?>
                                    <span class="text-muted">-</span>
                                <?php elseif ($punishment['until']): ?>
                                    <?php 
                                    if (strpos($punishment['until'], $lang->get('punishment.permanent')) !== false): ?>
                                        <span class="badge bg-danger"><?= $punishment['until'] ?></span>
                                    <?php elseif (strpos($punishment['until'], $lang->get('punishment.expired')) !== false): ?>
                                        <span class="badge bg-success"><?= $punishment['until'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?= $punishment['until'] ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $lang->get('punishment.permanent') ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php if ($type === 'kicks'): ?>
                                <span class="status-badge status-expired"><?= $lang->get('status.completed') ?></span>
                            <?php elseif ($punishment['active']): ?>
                                <span class="status-badge status-active"><?= $lang->get('status.active') ?></span>
                            <?php else: ?>
                                <span class="status-badge status-inactive"><?= $lang->get('status.inactive') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-punishment-list">
        <?php foreach ($punishments as $punishment): ?>
            <div class="mobile-punishment-card" onclick="window.location='<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>'">
                <div class="card">
                    <div class="card-body">
                        <!-- Player Info Header -->
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <i class="fas fa-user-circle" style="font-size:2.5rem;color:var(--text-muted);"></i>
                            <div style="flex:1;min-width:0;">
                                <h6 style="margin:0;font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($punishment['name'], ENT_QUOTES, 'UTF-8') ?></h6>
                                <small style="color:var(--text-muted);font-size:0.75rem;">#<?= $punishment['id'] ?></small>
                            </div>
                            <?php if ($type === 'kicks'): ?>
                                <span class="badge" style="background:rgba(107,114,128,0.2);color:#9ca3af;"><?= $lang->get('status.completed') ?></span>
                            <?php elseif ($punishment['active']): ?>
                                <span class="badge" style="background:rgba(239,68,68,0.2);color:#ef4444;"><?= $lang->get('status.active') ?></span>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(34,197,94,0.2);color:#22c55e;"><?= $lang->get('status.inactive') ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Reason -->
                        <div style="margin-top:0.75rem;padding:0.65rem;background:var(--bg-tertiary);border-radius:10px;">
                            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.03em;margin-bottom:0.25rem;"><?= $lang->get('table.reason') ?></div>
                            <div style="font-size:0.85rem;color:var(--text-primary);word-wrap:break-word;"><?= htmlspecialchars($punishment['reason'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        
                        <!-- Meta Information Grid -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:0.75rem;">
                            <div>
                                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.03em;"><?= $lang->get('table.staff') ?></div>
                                <div style="font-size:0.85rem;color:var(--primary);font-weight:500;"><?= htmlspecialchars($punishment['staff'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <div>
                                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.03em;"><?= $lang->get('table.date') ?></div>
                                <div style="font-size:0.85rem;color:var(--text-primary);font-weight:500;"><?= date('M j, Y', strtotime($punishment['date'])) ?></div>
                            </div>
                        </div>
                        
                        <!-- Footer with View Button -->
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.85rem;padding-top:0.75rem;border-top:1px solid var(--border-color);">
                            <?php if ($type !== 'kicks'): ?>
                                <?php if ($punishment['until']): ?>
                                    <?php if (strpos($punishment['until'], $lang->get('punishment.permanent')) !== false): ?>
                                        <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.7rem;"><?= $punishment['until'] ?></span>
                                    <?php elseif (strpos($punishment['until'], $lang->get('punishment.expired')) !== false): ?>
                                        <span class="badge" style="background:rgba(34,197,94,0.15);color:#22c55e;font-size:0.7rem;"><?= $punishment['until'] ?></span>
                                    <?php else: ?>
                                        <span class="badge" style="background:rgba(245,158,11,0.15);color:#f59e0b;font-size:0.7rem;"><?= $punishment['until'] ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.7rem;"><?= $lang->get('punishment.permanent') ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <a href="<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>" 
                               class="btn-view" onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i> <?= $lang->get('table.view') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total'] > 1): ?>
        <nav aria-label="<?= $lang->get('pagination.label') ?>">
            <div class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= $pagination['prev_url'] ?>" class="btn">
                        <i class="fas fa-chevron-left"></i>
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.previous') ?></span>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <i class="fas fa-chevron-left"></i>
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.previous') ?></span>
                    </span>
                <?php endif; ?>
                
                <span class="pagination-info">
                    <?= $lang->get('pagination.page_info', [
                        'current' => $pagination['current'],
                        'total' => $pagination['total']
                    ]) ?>
                </span>
                
                <?php if ($pagination['has_next']): ?>
                    <a href="<?= $pagination['next_url'] ?>" class="btn">
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.next') ?></span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.next') ?></span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
<?php endif; ?>
</div><!-- .punishments-page -->
