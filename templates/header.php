<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https: http:; connect-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com https://fonts.gstatic.com data:;">
    <meta name="csrf-token" content="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-path" content="<?= htmlspecialchars($config['base_path'], ENT_QUOTES, 'UTF-8') ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($config['site_robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- SEO Meta Tags -->
    <title><?php 
        if (isset($title)) {
            if (!empty($config['site_title_template'])) {
                echo htmlspecialchars(str_replace(['{page}', '{site}'], [$title, $config['site_name']], $config['site_title_template']), ENT_QUOTES, 'UTF-8');
            } else {
                echo htmlspecialchars($title . ' - ' . $config['site_name'], ENT_QUOTES, 'UTF-8');
            }
        } else {
            echo htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8');
        }
    ?></title>
    <meta name="description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Canonical URL -->
    <?php
    $canonicalUrl = rtrim($config['site_url'] ?? '', '/');
    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $canonicalUrl = $canonicalUrl . $requestUri;
    ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($config['site_favicon'] ?? asset('favicon.ico'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($config['site_apple_icon'] ?? asset('apple-touch-icon.png'), ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($config['site_url'] . $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_og_image'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_twitter_site'])): ?>
    <meta name="twitter:site" content="<?= htmlspecialchars($config['site_twitter_site'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Preconnect to external resources - prioritized -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Critical CSS inline for faster FCP -->
    <style>
        :root{--primary:#7c3aed;--bg-primary:#09090b;--bg-secondary:#18181b;--text-primary:#fafafa;--text-secondary:#a1a1aa;--border-color:#27272a;--card-bg:#18181b}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;background:var(--bg-primary);color:var(--text-primary);line-height:1.6;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
        .container{max-width:1400px;margin:0 auto;padding:0 1rem}
        .navbar-modern{position:sticky;top:0;z-index:200;background:rgba(9,9,11,0.9);-webkit-backdrop-filter:blur(20px);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-color)}
        .main-content{min-height:100vh;padding:2rem}
        .card{background:rgba(24,24,27,0.8);-webkit-backdrop-filter:blur(20px);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.05);border-radius:24px}
        .btn{display:-webkit-inline-flex;display:inline-flex;-webkit-align-items:center;align-items:center;gap:0.5rem;padding:0.75rem 1.25rem;border-radius:16px;font-weight:500;text-decoration:none;-webkit-transition:all 0.15s;transition:all 0.15s}
        .theme-light{--bg-primary:#f8fafc;--bg-secondary:#ffffff;--text-primary:#0f172a;--text-secondary:#475569;--border-color:#e2e8f0;--card-bg:#ffffff}
        .theme-light .navbar-modern{background:rgba(248,250,252,0.85)}
        .theme-light .card{background:rgba(255,255,255,0.8)}
        
        /* Mobile Menu - Critical */
        @media(max-width:991px){
            .navbar-toggler{display:-webkit-flex!important;display:flex!important}
            .navbar-collapse{display:none!important;position:fixed!important;top:70px!important;left:0!important;right:0!important;bottom:0!important;width:100vw!important;height:calc(100vh - 70px)!important;background:var(--bg-primary)!important;z-index:9999!important;-webkit-flex-direction:column!important;flex-direction:column!important;-webkit-justify-content:flex-start!important;justify-content:flex-start!important;padding:0!important;overflow-y:auto!important;-webkit-overflow-scrolling:touch}
            .navbar-collapse.show{display:-webkit-flex!important;display:flex!important}
            .navbar-collapse>.navbar-nav{display:-webkit-flex!important;display:flex!important;-webkit-flex-direction:column!important;flex-direction:column!important;width:100%!important;padding:1rem!important;gap:0.5rem!important;background:transparent!important;border-radius:0!important;margin:0!important;-webkit-flex-shrink:0!important;flex-shrink:0!important}
            .navbar-collapse .nav-item{width:100%!important;display:block!important}
            .navbar-collapse .nav-link{display:-webkit-flex!important;display:flex!important;width:100%!important;padding:1rem 1.25rem!important;font-size:1.1rem!important;color:var(--text-primary)!important;background:var(--card-bg)!important;border:1px solid var(--border-color)!important;border-radius:12px!important;-webkit-align-items:center!important;align-items:center!important;gap:0.75rem!important;text-decoration:none!important}
            .navbar-collapse .nav-link i{color:var(--primary)!important;width:24px!important;text-align:center!important}
            .navbar-collapse .nav-link span{color:var(--text-primary)!important;-webkit-flex:1!important;flex:1!important}
            .navbar-collapse .nav-link.active{background:var(--primary)!important;border-color:var(--primary)!important}
            .navbar-collapse .nav-link.active,.navbar-collapse .nav-link.active i,.navbar-collapse .nav-link.active span{color:#fff!important}
            .navbar-collapse>.navbar-controls{width:100%!important;padding:1rem!important;border-top:1px solid var(--border-color)!important;display:-webkit-flex!important;display:flex!important;-webkit-justify-content:center!important;justify-content:center!important;background:var(--bg-secondary)!important;margin-top:auto!important;-webkit-flex-shrink:0!important;flex-shrink:0!important}
            body.menu-open{overflow:hidden!important;position:fixed!important;width:100%!important;height:100%!important}
        }
    </style>
    
    <!-- Bootstrap CSS - high priority -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome - deferred -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"></noscript>
    
    <!-- Google Fonts - optional, loaded async -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Custom CSS - single file, no @imports -->
    <link href="<?= htmlspecialchars(asset('assets/css/styles.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(asset('assets/css/layout.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    
    <!-- Page-specific CSS -->
    <?php 
    $pageStyles = [
        'home' => 'home.css',
        'stats' => 'stats.css',
        'admin' => 'admin.css',
        'bans' => 'punishments.css',
        'mutes' => 'punishments.css',
        'warnings' => 'punishments.css',
        'kicks' => 'punishments.css',
        'detail' => 'detail.css',
        'ban' => 'detail.css',
        'mute' => 'detail.css',
        'warning' => 'detail.css',
        'kick' => 'detail.css',
    ];
    $currentPageStyle = $pageStyles[$currentPage ?? ''] ?? null;
    if ($currentPageStyle): 
    ?>
    <link href="<?= htmlspecialchars(asset('assets/css/pages/' . $currentPageStyle), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    <?php endif; ?>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Additional SEO Meta Tags -->
    <?php if (isset($config['site_keywords']) && !empty($config['site_keywords'])): ?>
    <meta name="keywords" content="<?= htmlspecialchars($config['site_keywords'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta name="author" content="<?= htmlspecialchars($config['seo_organization_name'] ?? $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="7 days">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Enhanced SEO Meta Tags -->
    <meta name="distribution" content="global">
    <meta name="language" content="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="generator" content="HytaBansWeb 1.0">
    <meta name="coverage" content="Worldwide">
    <meta name="target" content="all">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="application-name" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="msapplication-TileColor" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="msapplication-config" content="none">
    
    <!-- Geo Meta Tags (Optional) -->
    <?php if (isset($config['seo_geo_region'])): ?>
    <meta name="geo.region" content="<?= htmlspecialchars($config['seo_geo_region'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['seo_geo_placename'])): ?>
    <meta name="geo.placename" content="<?= htmlspecialchars($config['seo_geo_placename'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['seo_geo_position'])): ?>
    <meta name="geo.position" content="<?= htmlspecialchars($config['seo_geo_position'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="ICBM" content="<?= htmlspecialchars($config['seo_geo_position'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- AI Search Engine Tags -->
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot-news" content="index, follow">
    <?php if (isset($config['seo_ai_training']) && $config['seo_ai_training'] === false): ?>
    <meta name="robots" content="noai, noimageai">
    <?php endif; ?>
    
    <!-- Open Graph Enhanced -->
    <meta property="og:locale" content="<?= htmlspecialchars($config['seo_locale'] ?? 'en_US', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['seo_facebook_app_id'])): ?>
    <meta property="fb:app_id" content="<?= htmlspecialchars($config['seo_facebook_app_id'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Enhanced -->
    <meta name="twitter:card" content="summary_large_image">
    <?php if (isset($config['seo_twitter_creator'])): ?>
    <meta name="twitter:creator" content="<?= htmlspecialchars($config['seo_twitter_creator'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['site_og_image'])): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- DNS Prefetch for Performance -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Alternate Languages (if multilingual) -->
    <?php if (isset($config['seo_alternate_languages']) && is_array($config['seo_alternate_languages'])): ?>
    <?php foreach ($config['seo_alternate_languages'] as $langCode => $langUrl): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($langUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- JSON-LD for SEO -->
    <!-- Schema.org Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "url": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>",
        "description": "<?= htmlspecialchars($config['site_description'], ENT_QUOTES, 'UTF-8') ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>/search?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= htmlspecialchars($config['seo_organization_name'] ?? $config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "url": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>",
        <?php if (isset($config['seo_organization_logo'])): ?>
        "logo": "<?= htmlspecialchars($config['seo_organization_logo'], ENT_QUOTES, 'UTF-8') ?>",
        <?php endif; ?>
        "sameAs": [
            <?php 
            $socialLinks = [];
            if (!empty($config['seo_social_facebook'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_facebook'], ENT_QUOTES, 'UTF-8') . '"';
            if (!empty($config['seo_social_twitter'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_twitter'], ENT_QUOTES, 'UTF-8') . '"';
            if (!empty($config['seo_social_youtube'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_youtube'], ENT_QUOTES, 'UTF-8') . '"';
            echo implode(',', $socialLinks);
            ?>
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "<?= htmlspecialchars($config['seo_contact_type'] ?? 'customer service', ENT_QUOTES, 'UTF-8') ?>",
            <?php if (isset($config['seo_contact_phone'])): ?>
            "telephone": "<?= htmlspecialchars($config['seo_contact_phone'], ENT_QUOTES, 'UTF-8') ?>",
            <?php endif; ?>
            <?php if (isset($config['seo_contact_email'])): ?>
            "email": "<?= htmlspecialchars($config['seo_contact_email'], ENT_QUOTES, 'UTF-8') ?>"
            <?php endif; ?>
        }
    }
    </script>
    
    <?php if (isset($config['seo_enable_breadcrumbs']) && $config['seo_enable_breadcrumbs'] && isset($breadcrumbs) && is_array($breadcrumbs)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            <?php 
            $breadcrumbItems = [];
            foreach ($breadcrumbs as $index => $crumb) {
                $breadcrumbItems[] = '{
                    "@type": "ListItem",
                    "position": ' . ($index + 1) . ',
                    "name": "' . htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8') . '",
                    "item": "' . htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8') . '"
                }';
            }
            echo implode(',', $breadcrumbItems);
            ?>
        ]
    }
    </script>
    <?php endif; ?>
    
    <?php if (($currentPage ?? '') === 'home' || !isset($currentPage)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "applicationCategory": "Game",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "<?= htmlspecialchars($config['seo_price_currency'] ?? 'EUR', ENT_QUOTES, 'UTF-8') ?>"
        }
    }
    </script>
    <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($theme->getThemeClasses()['body'], ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- MOBILE MENU OVERLAY - Completely separate from navbar -->
    <div id="mobileMenuOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;background:#09090b;z-index:99999;flex-direction:column;overflow-y:auto;">
        <!-- Close button -->
        <div style="display:flex;justify-content:flex-end;padding:1rem 1.5rem;border-bottom:1px solid #27272a;">
            <button id="mobileMenuClose" style="background:#27272a;border:none;color:#fafafa;width:44px;height:44px;border-radius:12px;font-size:1.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Menu items -->
        <nav style="padding:1rem;display:flex;flex-direction:column;gap:0.5rem;">
            <a href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-home" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.home'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <a href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'bans' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-ban" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.bans'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <a href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'mutes' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-volume-mute" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <a href="<?= htmlspecialchars(url('warnings'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'warnings' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-exclamation-triangle" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <a href="<?= htmlspecialchars(url('kicks'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'kicks' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-sign-out-alt" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <?php if ($config['show_menu_stats'] ?? true): ?>
            <a href="<?= htmlspecialchars(url('stats'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'stats' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-chart-bar" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.statistics'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <?php endif; ?>
            <?php if ($config['show_menu_protest'] ?? true): ?>
            <a href="<?= htmlspecialchars(url('protest'), ENT_QUOTES, 'UTF-8') ?>" class="mobile-menu-link <?= ($currentPage ?? '') === 'protest' ? 'active' : '' ?>" style="display:flex;align-items:center;gap:0.75rem;padding:1rem 1.25rem;background:#18181b;border:1px solid #27272a;border-radius:12px;color:#fafafa;text-decoration:none;font-size:1.1rem;">
                <i class="fas fa-gavel" style="color:#7c3aed;width:24px;text-align:center;"></i>
                <span><?= htmlspecialchars($lang->get('nav.protest'), ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <?php endif; ?>
        </nav>
        <!-- Theme toggle at bottom -->
        <div style="margin-top:auto;padding:1rem;border-top:1px solid #27272a;background:#18181b;display:flex;justify-content:center;">
            <div class="theme-toggle-wrapper">
                <input type="checkbox" id="theme-toggle-mobile" class="theme-toggle-checkbox" <?= $theme->getCurrentTheme() === 'dark' ? 'checked' : '' ?>>
                <label for="theme-toggle-mobile" class="theme-toggle-label">
                    <i class="fas fa-sun"></i>
                    <i class="fas fa-moon"></i>
                    <span class="theme-toggle-ball"></span>
                </label>
            </div>
        </div>
    </div>
    <style>
        #mobileMenuOverlay .mobile-menu-link.active { background:#7c3aed !important; border-color:#7c3aed !important; }
        #mobileMenuOverlay .mobile-menu-link.active i { color:#fff !important; }
        #mobileMenuOverlay .mobile-menu-link:hover { background:#27272a; }
        @media(min-width:992px) { #mobileMenuOverlay { display:none !important; } }
        @media(max-width:991px) { 
            #navbarNav, .navbar-collapse { display:none !important; visibility:hidden !important; opacity:0 !important; position:absolute !important; left:-9999px !important; } 
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('mobileMenuOverlay');
        var toggler = document.getElementById('mobileMenuToggle');
        var closeBtn = document.getElementById('mobileMenuClose');
        var themeToggleMobile = document.getElementById('theme-toggle-mobile');
        var themeToggleDesktop = document.getElementById('theme-toggle');
        
        if (toggler && overlay) {
            toggler.addEventListener('click', function(e) {
                e.preventDefault();
                overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }
        
        if (closeBtn && overlay) {
            closeBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            });
        }
        
        // Sync theme toggles
        if (themeToggleMobile && themeToggleDesktop) {
            themeToggleMobile.addEventListener('change', function() {
                themeToggleDesktop.checked = this.checked;
                themeToggleDesktop.dispatchEvent(new Event('change'));
            });
        }
        
        // Close on link click
        var links = overlay ? overlay.querySelectorAll('.mobile-menu-link') : [];
        links.forEach(function(link) {
            link.addEventListener('click', function() {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            });
        });
        
        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.style.display === 'flex') {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });
    </script>

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-modern" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="navbar-brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 4L14 4L16 8L14 12L8 12L6 8L8 4Z" fill="currentColor"/>
                        <rect x="10" y="12" width="4" height="9" rx="1" fill="currentColor"/>
                        <path d="M9 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <span><?= htmlspecialchars($config['site_name'] ?? 'HytaBans', ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            
            <!-- Mobile Right Controls (Language + Hamburger) -->
            <div class="navbar-mobile-controls">
                <!-- Language Switcher - Always visible -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-navbar dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php 
                        $currentLang = $lang->getCurrentLanguage();
                        $langNames = [
                            'ar' => 'AR', 'cs' => 'CS', 'de' => 'DE', 'gr' => 'GR', 'en' => 'EN',
                            'es' => 'ES', 'fr' => 'FR', 'hu' => 'HU', 'it' => 'IT', 'ja' => 'JA',
                            'pl' => 'PL', 'ro' => 'RO', 'ru' => 'RU', 'sk' => 'SK', 'sr' => 'SR',
                            'tr' => 'TR', 'cn' => 'CN'
                        ];
                        ?>
                        <i class="fas fa-globe"></i>
                        <span><?= $langNames[$currentLang] ?? 'EN' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php 
                        // Get current URL path without query string
                        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                        foreach ($lang->getSupportedLanguages() as $langCode): 
                        ?>
                            <li>
                                <a class="dropdown-item <?= $currentLang === $langCode ? 'active' : '' ?>" 
                                   href="<?= htmlspecialchars($currentPath . '?lang=' . $langCode, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= $langNames[$langCode] ?? strtoupper($langCode) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="navbar-toggler" type="button" id="mobileMenuToggle" aria-label="Toggle navigation">
                    <span class="toggler-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>
            
            <!-- Navbar Content -->
            <div class="navbar-collapse" id="navbarNav" style="justify-content:flex-start!important;align-content:flex-start!important;">
                <ul class="navbar-nav" style="margin:0!important;justify-content:flex-start!important;align-content:flex-start!important;">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-home"></i>
                            <span><?= htmlspecialchars($lang->get('nav.home'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'bans' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-ban"></i>
                            <span><?= htmlspecialchars($lang->get('nav.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['bans_active']) && $GLOBALS['stats']['bans_active'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['bans_active'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'mutes' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-volume-mute"></i>
                            <span><?= htmlspecialchars($lang->get('nav.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['mutes_active']) && $GLOBALS['stats']['mutes_active'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['mutes_active'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'warnings' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('warnings'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= htmlspecialchars($lang->get('nav.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['warnings']) && $GLOBALS['stats']['warnings'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['warnings'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'kicks' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('kicks'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-sign-out-alt"></i>
                            <span><?= htmlspecialchars($lang->get('nav.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['kicks']) && $GLOBALS['stats']['kicks'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['kicks'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($config['show_menu_stats'] ?? true): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'stats' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('stats'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span><?= htmlspecialchars($lang->get('nav.statistics'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($config['show_menu_protest'] ?? true): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'protest' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('protest'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-gavel"></i>
                            <span><?= htmlspecialchars($lang->get('nav.protest'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (($config['show_menu_admin'] ?? true) && ($config['admin_enabled'] ?? false)): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('admin'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-cog"></i>
                            <span><?= htmlspecialchars($lang->get('nav.admin'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Theme Toggle in Mobile Menu -->
                <div class="navbar-controls">
                    <div class="theme-toggle-wrapper">
                        <input type="checkbox" id="theme-toggle" class="theme-toggle-checkbox" 
                               <?= $theme->getCurrentTheme() === 'dark' ? 'checked' : '' ?>>
                        <label for="theme-toggle" class="theme-toggle-label">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                            <span class="theme-toggle-ball"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Toast Notifications Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toast-notification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-info-circle me-2" id="toast-icon"></i>
                <strong class="me-auto" id="toast-title">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-message"></div>
        </div>
    </div>
    
    <!-- Hero Gradient Background -->
    <div class="hero-gradient"></div>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page content will be inserted here -->
