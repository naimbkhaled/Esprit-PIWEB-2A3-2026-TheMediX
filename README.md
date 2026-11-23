# MyApp - Sessions and PHP security with user management

Structure follows the workshop: MVC, sessions, password hashing, prepared statements, XSS and CSRF protection, front office and back office.

## Setup

1. Copy the `myapp` folder into your server directory (`htdocs` for XAMPP, `www` for WAMP).
2. For a fresh install, import `myapp1122/database.sql` in phpMyAdmin. If the `session.users` table already exists, import `myapp1122/upgrade.sql` to add the profile table and account-active flag (safe to re-import).
3. Adjust the credentials in `config/database.php` if your MySQL setup differs.
4. Open `http://localhost/myapp/index.php`.

## Using it

1. The site opens on the login screen. Choose **Créer un compte** to register as a client or doctor, then log in using the email and password you created.
2. Client and doctor profile details are saved alongside the shared login account; both use the existing user dashboard.
3. Click the profile icon at the top right to open the menu:
   - Consult my information
   - Edit my information (username, email, optional password change)
   - Log out
4. To get an administrator, change the `role` column to `admin` in phpMyAdmin and log in again. The admin gets the backoffice with the same profile menu.

## MVC split

- `models/UserModel.php`: attributes, getters, setters, `fromArray()`.
- `controllers/AuthController.php`: register, login, logout, sessions, roles, CSRF.
- `controllers/UserController.php`: read and update the connected user's profile.
- `views/`: HTML only, every output escaped with `htmlspecialchars()`.
- `index.php`: single entry point and router.
