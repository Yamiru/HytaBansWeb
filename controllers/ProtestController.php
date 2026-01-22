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

class ProtestController extends BaseController
{
    public function index(): void
    {
        // Get Discord/Email/Forum links from config
        $protestConfig = [
            'discord_link' => $this->config['protest_discord'] ?? '#',
            'email_address' => $this->config['protest_email'] ?? 'admin@example.com',
            'forum_link' => $this->config['protest_forum'] ?? '#'
        ];
        
        $this->render('protest', [
            'title' => $this->lang->get('protest.title'),
            'currentPage' => 'protest',
            'protestConfig' => $protestConfig
        ]);
    }
    
    public function submit(): void
    {
        // This method can be implemented later if you want to add direct form submission
        $this->jsonResponse([
            'success' => false,
            'message' => $this->lang->get('protest.form_not_available')
        ]);
    }
}