<div class="protest-page">
    <!-- Header -->
    <div class="protest-header">
        <div class="protest-header-content">
            <h1 class="protest-title">
                <span class="protest-title-icon">
                    <i class="fas fa-gavel"></i>
                </span>
                <?= htmlspecialchars($lang->get('protest.title'), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="protest-subtitle"><?= htmlspecialchars($lang->get('protest.description'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- How To Section -->
    <div class="protest-section">
        <div class="section-header-tech">
            <div class="section-title-wrapper">
                <span class="section-icon"><i class="fas fa-info-circle"></i></span>
                <h2 class="section-title"><?= htmlspecialchars($lang->get('protest.how_to_title'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
        </div>
        
        <p class="protest-lead"><?= htmlspecialchars($lang->get('protest.how_to_subtitle'), ENT_QUOTES, 'UTF-8') ?></p>
        
        <!-- Step 1 -->
        <div class="protest-step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h4 class="step-title">
                    <i class="fas fa-clipboard-list"></i>
                    <?= htmlspecialchars($lang->get('protest.step1_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p class="step-desc"><?= htmlspecialchars($lang->get('protest.step1_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                <ul class="step-list">
                    <?php 
                    $step1Items = $lang->get('protest.step1_items');
                    if (is_array($step1Items)):
                        foreach ($step1Items as $item): ?>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></span>
                            </li>
                        <?php endforeach;
                    endif; ?>
                </ul>
            </div>
        </div>

        <!-- Step 2: Contact Methods -->
        <div class="protest-step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h4 class="step-title">
                    <i class="fas fa-envelope"></i>
                    <?= htmlspecialchars($lang->get('protest.step2_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p class="step-desc"><?= htmlspecialchars($lang->get('protest.step2_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                
                <div class="contact-grid">
                    <!-- Discord -->
                    <?php if (($config['show_contact_discord'] ?? true)): ?>
                    <div class="contact-card contact-discord">
                        <div class="contact-card-glow"></div>
                        <div class="contact-icon-wrapper">
                            <i class="fab fa-discord"></i>
                        </div>
                        <h5 class="contact-title"><?= htmlspecialchars($lang->get('protest.discord_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="contact-desc"><?= htmlspecialchars($lang->get('protest.discord_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if ($protestConfig['discord_link'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($protestConfig['discord_link'], ENT_QUOTES, 'UTF-8') ?>" 
                               class="contact-btn btn-discord" target="_blank" rel="noopener">
                                <i class="fab fa-discord"></i>
                                <?= htmlspecialchars($lang->get('protest.discord_button'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Email -->
                    <?php if (($config['show_contact_email'] ?? true)): ?>
                    <div class="contact-card contact-email">
                        <div class="contact-card-glow"></div>
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="contact-title"><?= htmlspecialchars($lang->get('protest.email_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="contact-desc"><?= htmlspecialchars($lang->get('protest.email_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="email-box">
                            <code><?= htmlspecialchars($protestConfig['email_address'], ENT_QUOTES, 'UTF-8') ?></code>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Forum -->
                    <?php if (($config['show_contact_forum'] ?? true)): ?>
                    <div class="contact-card contact-forum">
                        <div class="contact-card-glow"></div>
                        <div class="contact-icon-wrapper">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h5 class="contact-title"><?= htmlspecialchars($lang->get('protest.forum_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="contact-desc"><?= htmlspecialchars($lang->get('protest.forum_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if ($protestConfig['forum_link'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($protestConfig['forum_link'], ENT_QUOTES, 'UTF-8') ?>" 
                               class="contact-btn btn-forum" target="_blank" rel="noopener">
                                <i class="fas fa-comments"></i>
                                <?= htmlspecialchars($lang->get('protest.forum_button'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="protest-step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h4 class="step-title">
                    <i class="fas fa-edit"></i>
                    <?= htmlspecialchars($lang->get('protest.step3_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p class="step-desc"><?= htmlspecialchars($lang->get('protest.step3_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                <ul class="step-list step-list-arrow">
                    <?php 
                    $step3Items = $lang->get('protest.step3_items');
                    if (is_array($step3Items)):
                        foreach ($step3Items as $item): ?>
                            <li>
                                <i class="fas fa-arrow-right"></i>
                                <span><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></span>
                            </li>
                        <?php endforeach;
                    endif; ?>
                </ul>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="protest-step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h4 class="step-title">
                    <i class="fas fa-clock"></i>
                    <?= htmlspecialchars($lang->get('protest.step4_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p class="step-desc"><?= htmlspecialchars($lang->get('protest.step4_desc'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>

    <!-- Guidelines -->
    <div class="protest-section guidelines-section">
        <div class="section-header-tech">
            <div class="section-title-wrapper">
                <span class="section-icon text-info"><i class="fas fa-book"></i></span>
                <h2 class="section-title"><?= htmlspecialchars($lang->get('protest.guidelines_title'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
        </div>
        
        <ul class="guidelines-list">
            <?php 
            $guidelinesItems = $lang->get('protest.guidelines_items');
            if (is_array($guidelinesItems)):
                foreach ($guidelinesItems as $guideline): ?>
                    <li>
                        <i class="fas fa-check"></i>
                        <span><?= htmlspecialchars($guideline, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                <?php endforeach;
            endif; ?>
        </ul>
    </div>

    <!-- Warning -->
    <div class="protest-warning">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="warning-content">
            <h5 class="warning-title"><?= htmlspecialchars($lang->get('protest.warning_title'), ENT_QUOTES, 'UTF-8') ?></h5>
            <p class="warning-text"><?= htmlspecialchars($lang->get('protest.warning_desc'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>
</div>

<!-- Protest Page Styles - 科技感设计 -->
<style>
.protest-page {
    max-width: 1000px;
    margin: 0 auto;
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Header */
.protest-header {
    text-align: center;
    margin-bottom: 3rem;
}

.protest-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.protest-title-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, var(--primary), #06b6d4);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.4rem;
    box-shadow: 0 0 30px rgba(124, 58, 237, 0.4);
}

.protest-subtitle {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin: 0;
}

/* Section */
.protest-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.section-header-tech {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.section-title-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(6, 182, 212, 0.15));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1rem;
}

.section-icon.text-info {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.15);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.protest-lead {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* Steps */
.protest-step {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.protest-step:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.step-number {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary), #06b6d4);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

.step-content {
    flex: 1;
}

.step-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.step-title i {
    color: var(--primary);
}

.step-desc {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.step-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.step-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0;
    color: var(--text-primary);
}

.step-list li i {
    color: #22c55e;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.step-list-arrow li i {
    color: var(--primary);
}

.step-list li span {
    flex: 1;
}

/* Contact Cards */
.contact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 1.5rem;
}

@media (max-width: 991px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

.contact-card {
    position: relative;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    overflow: hidden;
}

.contact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    border-radius: 16px 16px 0 0;
}

.contact-discord::before { background: linear-gradient(90deg, #7289DA, #5b6eae); }
.contact-email::before { background: linear-gradient(90deg, #EA4335, #c5362c); }
.contact-forum::before { background: linear-gradient(90deg, #FFA500, #e69500); }

.contact-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.contact-discord:hover { border-color: rgba(114, 137, 218, 0.5); }
.contact-email:hover { border-color: rgba(234, 67, 53, 0.5); }
.contact-forum:hover { border-color: rgba(255, 165, 0, 0.5); }

.contact-icon-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
    margin: 0 auto 1.25rem;
}

.contact-discord .contact-icon-wrapper { background: linear-gradient(135deg, #7289DA, #5b6eae); }
.contact-email .contact-icon-wrapper { background: linear-gradient(135deg, #EA4335, #c5362c); }
.contact-forum .contact-icon-wrapper { background: linear-gradient(135deg, #FFA500, #e69500); }

.contact-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.contact-desc {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.25rem;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.2s ease;
    color: #fff !important;
}

.btn-discord {
    background: linear-gradient(135deg, #7289DA, #5b6eae);
}

.btn-discord:hover {
    background: linear-gradient(135deg, #5b6eae, #4a5a8c);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(114, 137, 218, 0.4);
}

.btn-forum {
    background: linear-gradient(135deg, #FFA500, #e69500);
}

.btn-forum:hover {
    background: linear-gradient(135deg, #e69500, #cc8400);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
}

.email-box {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 0.75rem 1.25rem;
}

.email-box code {
    color: var(--primary);
    font-size: 1rem;
    font-weight: 600;
    background: none;
    padding: 0;
}

/* Guidelines */
.guidelines-section {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(6, 182, 212, 0.05));
    border-color: rgba(59, 130, 246, 0.2);
}

.guidelines-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 0.75rem;
}

.guidelines-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--bg-tertiary);
    border-radius: 10px;
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.guidelines-list li:hover {
    background: var(--hover-bg);
    transform: translateX(4px);
}

.guidelines-list li i {
    color: #3b82f6;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.guidelines-list li span {
    flex: 1;
}

/* Warning */
.protest-warning {
    display: flex;
    gap: 1.25rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 16px;
    border-left: 4px solid #ef4444;
}

.warning-icon {
    width: 48px;
    height: 48px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #ef4444;
    flex-shrink: 0;
}

.warning-content {
    flex: 1;
}

.warning-title {
    font-size: 1rem;
    font-weight: 700;
    color: #ef4444;
    margin-bottom: 0.5rem;
}

.warning-text {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

/* Mobile */
@media (max-width: 768px) {
    .protest-title {
        font-size: 1.5rem;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .protest-section {
        padding: 1.5rem;
    }
    
    .protest-step {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .contact-card {
        padding: 1.5rem;
    }
    
    .contact-icon-wrapper {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .protest-warning {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
