# MyApp — minimal MVC login (Nébula design)

PHP 8.1+ (pdo_mysql, curl) · MySQL/MariaDB · no Composer.

## Structure (workshop "Sessions et Sécurité PHP")
```
config/database.php        PDO Singleton (Database::getConnexion)
config/oauth.php           Google / GitHub / Discord client IDs + base_url
config/mail.php            Brevo API key + verified sender (password-reset emails)
models/UserModel.php       attributes + constructor + getters/setters only
controllers/AuthController.php   ALL logic (SQL, hash, sessions, CSRF, roles, OAuth)
views/                     layout.php (Nébula) · login · register · user_space (frontoffice)
                           layout_admin.php · admin_space (backoffice) · social.php (buttons)
assets/                    style.css + script.js (design, untouched) · validate.js (JS input check)
index.php                  single entry point + routing (?action=...)
oauth_callback.php         redirect URI for the providers (forwards to index.php)
database.sql               tables users, remember_tokens, password_resets
upgrade.sql                only the 2 new tables (if `users` already exists)
```

## Setup
1. Import `database.sql` in phpMyAdmin (creates DB `session`; change the name in `config/database.php` if needed).
2. Put the folder in `htdocs/myapp` and open `http://localhost/myapp/index.php`.
3. Admin: register, then `UPDATE users SET role='admin' WHERE email='you@example.com';`

## Google / GitHub / Discord
Fill `config/oauth.php` (`base_url` must match your URL, no trailing slash), then register this
**redirect URI** at each provider:

    <base_url>/oauth_callback.php        e.g. http://localhost/myapp/oauth_callback.php

| Provider | Console |
|---|---|
| Google  | console.cloud.google.com → APIs & Services → Credentials → OAuth client ID (Web) |
| GitHub  | Settings → Developer settings → OAuth Apps |
| Discord | discord.com/developers → your app → OAuth2 → Redirects |

A button whose provider is not configured shows an error message instead of redirecting.
On Windows/XAMPP, if cURL fails with an SSL error, set `curl.cainfo` in php.ini to a cacert.pem bundle.

## Input validation (not HTML)
Forms use `novalidate` and no `required`/`minlength`: checks are done in `assets/validate.js` (browser)
and again in `AuthController` (server, the one that really counts).

## Remember me / Forgot password
- "Se souvenir de moi": 30-day cookie (random selector + hashed validator in `remember_tokens`, rotated at each use).
- "Mot de passe oublié": emailed link valid 60 min, single use, token stored hashed in `password_resets`.
  Fill `config/mail.php` (Brevo API key + a sender verified in Brevo). While it is empty, the reset link is
  written to the Apache error log (`C:\xampp\apache\logs\error.log`) so you can test without email.
- Social buttons always show the provider's account picker (`prompt=select_account` for Google/GitHub,
  `prompt=consent` for Discord).
