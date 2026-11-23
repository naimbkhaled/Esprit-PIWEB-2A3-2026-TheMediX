<?php
/**
 * views/layout_admin.php
 * Dedicated BACKOFFICE layout (admin dashboard style with sidebar)
 * Expects: $title (string), $content (string), $activePage (string), $user (UserModel)
 */
$activePage = $activePage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Admin Panel', ENT_QUOTES, 'UTF-8') ?></title>
<style>
:root {
--sidebar-bg: #111827; --sidebar-bg-hover: #1f2937; --sidebar-text: #9ca3af;
--accent: #6366f1; --accent-dark: #4f46e5; --bg: #f3f4f6; --card-bg: #ffffff;
--border: #e5e7eb; --text: #111827; --text-light: #6b7280; --red: #dc2626; --radius: 12px;
}
* { box-sizing: border-box; }
body { margin: 0; font-family: -apple-system, 'Segoe UI', Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
.admin-wrapper { display: flex; min-height: 100vh; }
.sidebar { width: 240px; flex-shrink: 0; background: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; padding: 20px 0; }
.sidebar .brand { display: flex; align-items: center; gap: 10px; padding: 0 24px 24px; color: #fff; font-weight: 700; font-size: 17px; border-bottom: 1px solid #1f2937; margin-bottom: 16px; }
.sidebar .brand .dot { width: 10px; height: 10px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 4px rgba(99,102,241,0.2); }
.sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 11px 24px; color: var(--sidebar-text); text-decoration: none; font-size: 14px; border-left: 3px solid transparent; transition: all .15s; }
.sidebar nav a:hover { background: var(--sidebar-bg-hover); color: #fff; }
.sidebar nav a.active { background: var(--sidebar-bg-hover); color: #fff; border-left-color: var(--accent); }
.sidebar nav a .icon { font-size: 16px; width: 18px; text-align: center; }
.sidebar-footer { margin-top: auto; padding: 16px 24px 0; border-top: 1px solid #1f2937; }
.sidebar-footer .who { font-size: 13px; color: #fff; margin-bottom: 2px; }
.sidebar-footer .role-tag { font-size: 11px; color: var(--accent); text-transform: uppercase; letter-spacing: .5px; }
.sidebar-footer a.logout { display: inline-block; margin-top: 12px; font-size: 13px; color: var(--sidebar-text); text-decoration: none; }
.sidebar-footer a.logout:hover { color: #fff; }
.main { flex: 1; display: flex; flex-direction: column; }
.topbar { background: var(--card-bg); border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
.topbar h1 { font-size: 18px; margin: 0; }
.topbar .env-tag { font-size: 12px; font-weight: 600; color: var(--red); background: #fee2e2; padding: 4px 10px; border-radius: 20px; }
.content { padding: 32px; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
.stat-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; }
.stat-card .label { font-size: 13px; color: var(--text-light); }
.stat-card .value { font-size: 28px; font-weight: 700; margin-top: 6px; }
.stat-card .value.accent { color: var(--accent); }
.stat-card .value.red { color: var(--red); }
.card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
.card h2 { font-size: 15px; margin: 0 0 16px; }
table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid var(--border); font-size: 14px; }
th { color: var(--text-light); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .4px; }
tr:last-child td { border-bottom: none; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.badge-admin { background: #fee2e2; color: var(--red); }
.badge-user { background: #e0e7ff; color: var(--accent-dark); }
</style>
</head>
<body>
<div class="admin-wrapper">
<aside class="sidebar">
<div class="brand"><span class="dot"></span> MyApp Admin</div>
<nav>
<a href="index.php?action=admin" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>"><span class="icon">&#9632;</span> Dashboard</a>
<a href="index.php?action=admin" class="<?= $activePage === 'users' ? 'active' : '' ?>"><span class="icon">&#128101;</span> Users</a>
<a href="index.php?action=dashboard"><span class="icon">&#8617;</span> Back to frontoffice</a>
</nav>
<div class="sidebar-footer">
<div class="who"><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></div>
<div class="role-tag">Administrator</div>
<a class="logout" href="index.php?action=logout">Log out</a>
</div>
</aside>
<div class="main">
<div class="topbar">
<h1><?= htmlspecialchars($title ?? 'Admin Panel', ENT_QUOTES, 'UTF-8') ?></h1>
<span class="env-tag">BACKOFFICE</span>
</div>
<div class="content">
<?= $content ?>
</div>
</div>
</div>
</body>
</html>
