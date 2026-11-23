# MyApp - Sessions and PHP security with user management

Structure follows the workshop: MVC, sessions, password hashing, prepared statements, XSS and CSRF protection, front office and back office.

## Setup

1. Copy the `myapp` folder into your server directory (`htdocs` for XAMPP, `www` for WAMP).
2. In phpMyAdmin, import `database.sql`. It creates the database `session` and the table `users`.
3. Adjust the credentials in `config/database.php` if your MySQL setup differs.
4. Open `http://localhost/myapp/index.php`.

## Using it

1. Sign up, then log in. The main page is empty on purpose.
2. Click the profile icon at the top right to open the menu:
   - Consult my information
   - Edit my information (username, email, optional password change)
   - Log out
3. To get an administrator, change the `role` column to `admin` in phpMyAdmin and log in again. The admin gets the backoffice with the same profile menu.

## MVC split

- `models/UserModel.php`: attributes, getters, setters, `fromArray()`.
- `controllers/AuthController.php`: register, login, logout, sessions, roles, CSRF.
- `controllers/UserController.php`: read and update the connected user's profile.
- `views/`: HTML only, every output escaped with `htmlspecialchars()`.
- `index.php`: single entry point and router.
