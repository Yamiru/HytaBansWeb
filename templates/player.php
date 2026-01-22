<div class="player-page">
    <!-- Player Header -->
    <div class="player-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="player-icon-large">
                <img src="<?= htmlspecialchars($this->getPlayerIcon($player['player_name']), ENT_QUOTES, 'UTF-8') ?>" 
                     alt="<?= htmlspecialchars($player['player_name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="player-info-header">
                <h1 class="mb-1"><?= htmlspecialchars($player['player_name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-fingerprint"></i>
                    <code class="small"><?= htmlspecialchars($player['player_uuid'], ENT_QUOTES, 'UTF-8') ?></code>
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="player-stats-grid mb-4">
        <div class="player-stat-card bg-danger-subtle">
            <div class="stat-icon text-danger"><i class="fas fa-ban"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['bans'] ?></div>
                <div class="stat-label"><?= $lang->get('stats.bans') ?></div>
                <?php if ($stats['bans_active'] > 0): ?>
                <div class="stat-badge"><span class="badge bg-danger"><?= $stats['bans_active'] ?> active</span></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="player-stat-card bg-warning-subtle">
            <div class="stat-icon text-warning"><i class="fas fa-volume-mute"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['mutes'] ?></div>
                <div class="stat-label"><?= $lang->get('stats.mutes') ?></div>
                <?php if ($stats['mutes_active'] > 0): ?>
                <div class="stat-badge"><span class="badge bg-warning"><?= $stats['mutes_active'] ?> active</span></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="player-stat-card bg-info-subtle">
            <div class="stat-icon text-info"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['warnings'] ?></div>
                <div class="stat-label"><?= $lang->get('stats.warnings') ?></div>
            </div>
        </div>
        
        <div class="player-stat-card bg-secondary-subtle">
            <div class="stat-icon text-secondary"><i class="fas fa-sign-out-alt"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['kicks'] ?></div>
                <div class="stat-label"><?= $lang->get('stats.kicks') ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Punishment Timeline Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i>
                        Punishment Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($timeline)): ?>
                    <div class="timeline-chart">
                        <?php 
                        $maxTotal = max(array_column($timeline, 'total'));
                        foreach ($timeline as $month): 
                        ?>
                        <div class="timeline-bar-group">
                            <div class="timeline-bars">
                                <?php if ($month['bans'] > 0): ?>
                                <div class="timeline-bar bg-danger" 
                                     style="height: <?= min(100, ($month['bans'] / max(1, $maxTotal)) * 100) ?>%"
                                     title="<?= $month['bans'] ?> bans"></div>
                                <?php endif; ?>
                                <?php if ($month['mutes'] > 0): ?>
                                <div class="timeline-bar bg-warning" 
                                     style="height: <?= min(100, ($month['mutes'] / max(1, $maxTotal)) * 100) ?>%"
                                     title="<?= $month['mutes'] ?> mutes"></div>
                                <?php endif; ?>
                                <?php if ($month['warnings'] > 0): ?>
                                <div class="timeline-bar bg-info" 
                                     style="height: <?= min(100, ($month['warnings'] / max(1, $maxTotal)) * 100) ?>%"
                                     title="<?= $month['warnings'] ?> warnings"></div>
                                <?php endif; ?>
                                <?php if ($month['kicks'] > 0): ?>
                                <div class="timeline-bar bg-secondary" 
                                     style="height: <?= min(100, ($month['kicks'] / max(1, $maxTotal)) * 100) ?>%"
                                     title="<?= $month['kicks'] ?> kicks"></div>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-label"><?= htmlspecialchars($month['label'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="timeline-legend mt-3">
                        <span class="legend-item"><span class="legend-color bg-danger"></span> Bans</span>
                        <span class="legend-item"><span class="legend-color bg-warning"></span> Mutes</span>
                        <span class="legend-item"><span class="legend-color bg-info"></span> Warnings</span>
                        <span class="legend-item"><span class="legend-color bg-secondary"></span> Kicks</span>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                        <p>No punishment data available for timeline</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Player Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-info"></i>
                        Summary
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="summary-list">
                        <li>
                            <span class="summary-label">Total Punishments</span>
                            <span class="summary-value"><?= $stats['total'] ?></span>
                        </li>
                        <?php if ($stats['first_punishment']): ?>
                        <li>
                            <span class="summary-label">First Punishment</span>
                            <span class="summary-value"><?= date('M j, Y', $stats['first_punishment']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($stats['last_punishment']): ?>
                        <li>
                            <span class="summary-label">Last Punishment</span>
                            <span class="summary-value"><?= date('M j, Y', $stats['last_punishment']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($stats['most_common_reason']): ?>
                        <li>
                            <span class="summary-label">Common Reason</span>
                            <span class="summary-value text-truncate" title="<?= htmlspecialchars($stats['most_common_reason'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars(mb_substr($stats['most_common_reason'], 0, 30), ENT_QUOTES, 'UTF-8') ?>
                                <?php if (mb_strlen($stats['most_common_reason']) > 30): ?>...<?php endif; ?>
                            </span>
                        </li>
                        <?php endif; ?>
                        <?php if ($stats['most_active_staff']): ?>
                        <li>
                            <span class="summary-label">Most Active Staff</span>
                            <span class="summary-value"><?= htmlspecialchars($stats['most_active_staff'], ENT_QUOTES, 'UTF-8') ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Notes (Admin Only) -->
    <?php if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated']): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-sticky-note text-warning"></i>
                Staff Notes
            </h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                <i class="fas fa-plus"></i> Add Note
            </button>
        </div>
        <div class="card-body">
            <?php if (!empty($notes)): ?>
            <div class="notes-list">
                <?php foreach ($notes as $note): ?>
                <div class="note-item" data-note-id="<?= $note['id'] ?>">
                    <div class="note-header">
                        <span class="note-author">
                            <i class="fas fa-user-shield text-primary"></i>
                            <?= htmlspecialchars($note['staff_name'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="note-date">
                            <?= date('M j, Y H:i', $note['created_at']) ?>
                        </span>
                        <button class="btn btn-sm btn-link text-danger delete-note-btn" data-note-id="<?= $note['id'] ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="note-content"><?= nl2br(htmlspecialchars($note['note'], ENT_QUOTES, 'UTF-8')) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-sticky-note fa-2x mb-2 opacity-50"></i>
                <p>No staff notes yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus text-primary"></i> Add Staff Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addNoteForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= SecurityManager::getCsrfToken() ?>">
                        <input type="hidden" name="player_uuid" value="<?= htmlspecialchars($player['player_uuid'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" rows="4" required 
                                      placeholder="Enter your note about this player..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Punishment History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-history text-primary"></i>
                Punishment History
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($punishments)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Staff</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($punishments as $p): ?>
                        <?php
                            $type = $p['type'] ?? 'unknown';
                            $typeClass = match($type) {
                                'ban' => 'danger',
                                'mute' => 'warning',
                                'warning' => 'info',
                                'kick' => 'secondary',
                                default => 'dark'
                            };
                            $typeIcon = match($type) {
                                'ban' => 'fa-ban',
                                'mute' => 'fa-volume-mute',
                                'warning' => 'fa-exclamation-triangle',
                                'kick' => 'fa-sign-out-alt',
                                default => 'fa-question'
                            };
                            
                            $isActive = false;
                            $statusText = 'Expired';
                            $statusClass = 'secondary';
                            
                            if (($p['removed'] ?? 0) == 1) {
                                $statusText = 'Removed';
                                $statusClass = 'success';
                            } elseif (($p['permanent'] ?? 0) == 1) {
                                $isActive = true;
                                $statusText = 'Permanent';
                                $statusClass = 'danger';
                            } else {
                                $expiresAt = (int)($p['until'] ?? 0);
                                if ($expiresAt === 0 || $expiresAt === -1) {
                                    $isActive = true;
                                    $statusText = 'Permanent';
                                    $statusClass = 'danger';
                                } elseif ($expiresAt > time()) {
                                    $isActive = true;
                                    $statusText = 'Active';
                                    $statusClass = 'warning';
                                }
                            }
                            
                            if ($type === 'kick' || $type === 'warning') {
                                $statusText = '-';
                                $statusClass = 'secondary';
                            }
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-<?= $typeClass ?>">
                                    <i class="fas <?= $typeIcon ?>"></i>
                                    <?= ucfirst($type) ?>
                                </span>
                            </td>
                            <td class="reason-cell">
                                <?= htmlspecialchars($p['reason'] ?? 'No reason', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td><?= htmlspecialchars($p['staff_name'] ?? 'Console', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= date('M j, Y H:i', (int)($p['time'] ?? 0)) ?></td>
                            <td>
                                <?php if ($type !== 'kick'): ?>
                                    <?php if (($p['permanent'] ?? 0) == 1): ?>
                                        <span class="text-danger">Permanent</span>
                                    <?php elseif ((int)($p['until'] ?? 0) > 0): ?>
                                        <?= $this->formatDuration((int)($p['until'] ?? 0) - (int)($p['time'] ?? 0)) ?>
                                    <?php else: ?>
                                        <span class="text-danger">Permanent</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($statusText !== '-'): ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5>No punishments found</h5>
                <p class="text-muted">This player has a clean record</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add Note Form
    const addNoteForm = document.getElementById('addNoteForm');
    if (addNoteForm) {
        addNoteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            try {
                const response = await fetch('<?= $config['base_url'] ?? '' ?>?action=player-add-note', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to add note');
                }
            } catch (error) {
                alert('An error occurred');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // Delete Note
    document.querySelectorAll('.delete-note-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this note?')) return;
            
            const noteId = this.dataset.noteId;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;
            
            try {
                const response = await fetch('<?= $config['base_url'] ?? '' ?>?action=player-delete-note', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `csrf_token=${csrfToken}&note_id=${noteId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.closest('.note-item').remove();
                } else {
                    alert(data.message || 'Failed to delete note');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    });
});
</script>

<style>
.player-page { padding-bottom: 2rem; }

.player-header {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    border: 1px solid var(--border-color);
}

.player-icon-large img {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.player-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-md);
}

.player-stat-card {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-lg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.bg-danger-subtle { background: rgba(239, 68, 68, 0.1); }
.bg-warning-subtle { background: rgba(245, 158, 11, 0.1); }
.bg-info-subtle { background: rgba(6, 182, 212, 0.1); }
.bg-secondary-subtle { background: rgba(107, 114, 128, 0.1); }

.stat-icon {
    font-size: 2rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.timeline-chart {
    display: flex;
    align-items: flex-end;
    gap: var(--space-xs);
    height: 200px;
    padding: var(--space-md) 0;
}

.timeline-bar-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-xs);
    min-width: 40px;
}

.timeline-bars {
    display: flex;
    gap: 2px;
    align-items: flex-end;
    height: 150px;
    width: 100%;
}

.timeline-bar {
    flex: 1;
    min-height: 4px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.timeline-bar:hover {
    transform: scaleY(1.1);
    filter: brightness(1.1);
}

.timeline-label {
    font-size: 0.625rem;
    color: var(--text-secondary);
    text-align: center;
    white-space: nowrap;
}

.timeline-legend {
    display: flex;
    gap: var(--space-md);
    justify-content: center;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.summary-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.summary-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-sm) 0;
    border-bottom: 1px solid var(--border-color);
}

.summary-list li:last-child {
    border-bottom: none;
}

.summary-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.summary-value {
    font-weight: 600;
    max-width: 60%;
}

.notes-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.note-item {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    border: 1px solid var(--border-color);
}

.note-header {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    margin-bottom: var(--space-sm);
}

.note-author {
    font-weight: 600;
}

.note-date {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-left: auto;
}

.note-content {
    color: var(--text-primary);
    line-height: 1.5;
}

.reason-cell {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .player-icon-large img {
        width: 60px;
        height: 60px;
    }
    
    .player-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .timeline-chart {
        overflow-x: auto;
    }
    
    .timeline-label {
        font-size: 0.5rem;
    }
}
</style>
