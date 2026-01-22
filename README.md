# HytaBansWeb
A lightweight, php/mysql ban management website for Hytale servers using the Hytabans plugin.

![Version](https://img.shields.io/badge/version-1.0-orange)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## Screenshot
![Imgur Image](https://i.imgur.com/JYfXWsq.png)


## Live Demo
[https://yamiru.com/hytabans](https://yamiru.com/hytabans)

## Features

- 🎨 **Hytale-Inspired Design** - Beautiful orange/amber color scheme
- 🌙 **Dark/Light Theme** - Automatic theme switching
- 🌍 **Multi-language Support** - 17+ languages included
- 📱 **Fully Responsive** - Works on all devices
- 🔒 **Secure** - Built with security best practices
- ⚡ **Fast** - Optimized for performance

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ / MariaDB 10.3+
- PDO MySQL extension
- [HytaBans plugin](https://builtbybit.com/resources/hytabans.90673/) installed with mysql connection

## Installation

1. Download the latest release
2. open /install.php
3. Visit your website
you can enable normal password/google auth or discord auth


### Authentication Configuration

#### Traditional Password Login
```.env
# Admin Configuration
ADMIN_ENABLED=true
ADMIN_PASSWORD=

# Allow password login
ALLOW_PASSWORD_LOGIN=true

```

Generate admin password hash:
1. Open `https://yoursite.com/hash.php`
2. Enter your desired password
3. Copy the generated hash
4. Paste it into `.env` as `ADMIN_PASSWORD`
5. Delete `hash.php` file for security

#### Google OAuth Setup
```.env
# Google OAuth Configuration
GOOGLE_AUTH_ENABLED=true
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
```

To get Google OAuth credentials:
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `https://yoursite.com/admin/callback/google`

#### Discord OAuth Setup (Beta)
```.env
# Discord OAuth Configuration
DISCORD_AUTH_ENABLED=true
DISCORD_CLIENT_ID=your_discord_client_id
DISCORD_CLIENT_SECRET=your_discord_client_secret
```

To get Discord credentials:
1. Go to [Discord DEV portal](https://discord.com/developers/applications)
2. Create a new APP
3. Add name and create
4. click to OAuth2 and mark identify and email
5. Add redirect URI: `https://yoursite.com/admin/oauth-callback?provider=discord`



## Links

- **Repository**: [https://github.com/Yamiru/HytaBansWeb](https://github.com/Yamiru/HytaBansWeb)
- **Issues**: [https://github.com/Yamiru/HytaBansWeb/issues](https://github.com/Yamiru/HytaBansWeb/issues)
- **Author**: [https://yamiru.com](https://yamiru.com)

## License

MIT License - see [LICENSE](LICENSE) for details.
