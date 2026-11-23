<?php
/**
* views/layout.php
* Global HTML layout for the FRONTOFFICE (regular users)
* Expects: $title (string), $content (string, already generated/escaped)
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'MyApp', ENT_QUOTES, 'UTF-8') ?></title>
<style>
:root {
--chrome-bg: #f1f3f4;
--chrome-bar: #ffffff;
--chrome-border: #dadce0;
--chrome-blue: #1a73e8;
--chrome-blue-dark: #1558b0;
--chrome-text: #202124;
--chrome-text-light: #5f6368;
--chrome-red: #d93025;
--chrome-green: #188038;
--radius: 10px;
}
* { box-sizing: border-box; }
body {
margin: 0;
font-family: 'Google Sans', Roboto, Arial, sans-serif;
background: var(--chrome-bg);
color: var(--chrome-text);
}
.browser-bar {
background: var(--chrome-bar);
border-bottom: 1px solid var(--chrome-border);
padding: 10px 18px;
display: flex;
align-items: center;
gap: 10px;
box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}
.browser-dots span {
display: inline-block;
width: 12px; height: 12px;
border-radius: 50%;
margin-right: 6px;
}
.dot-red { background: #ff5f57; }
.dot-yellow { background: #febc2e; }
.dot-green { background: #28c840; }
.address-bar {
flex: 1;
background: var(--chrome-bg);
border: 1px solid var(--chrome-border);
border-radius: 20px;
padding: 6px 16px;
font-size: 13px;
color: var(--chrome-text-light);
}
nav.topnav {
background: var(--chrome-bar);
border-bottom: 1px solid var(--chrome-border);
padding: 12px 24px;
display: flex;
align-items: center;
justify-content: space-between;
}
nav.topnav .brand {
font-weight: 600;
font-size: 18px;
color: var(--chrome-blue);
}
nav.topnav .links a {
margin-left: 18px;
text-decoration: none;
color: var(--chrome-text);
font-size: 14px;
padding: 8px 14px;
border-radius: 20px;
transition: background .15s;
}
nav.topnav .links a:hover { background: var(--chrome-bg); }
nav.topnav .links a.btn-primary {
background: var(--chrome-blue);
color: #fff;
}
nav.topnav .links a.btn-primary:hover { background: var(--chrome-blue-dark); }
.container {
max-width: 480px;
margin: 60px auto;
background: #fff;
border: 1px solid var(--chrome-border);
border-radius: var(--radius);
padding: 36px;
box-shadow: 0 1px 3px rgba(60,64,67,0.15);
}
.container.wide { max-width: 800px; }
h1 { font-size: 22px; margin-top: 0; }
label { display: block; font-size: 13px; color: var(--chrome-text-light); margin: 14px 0
6px; }
input[type=text], input[type=email], input[type=password] {
width: 100%;
padding: 10px 12px;
border: 1px solid var(--chrome-border);
border-radius: 6px;
font-size: 14px;
outline: none;
}
input:focus { border-color: var(--chrome-blue); }
button {
margin-top: 22px;
width: 100%;
padding: 11px;
background: var(--chrome-blue);
color: #fff;
border: none;
border-radius: 6px;
font-size: 14px;
cursor: pointer;
}
button:hover { background: var(--chrome-blue-dark); }
.alert {
padding: 10px 14px;
border-radius: 6px;
font-size: 13px;
margin-bottom: 14px;
}
.alert-error { background: #fce8e6; color: var(--chrome-red); border: 1px solid #f6c1bc; }
.alert-success { background: #e6f4ea; color: var(--chrome-green); border: 1px solid
#b7dfc2; }
.switch-link { text-align: center; margin-top: 18px; font-size: 13px; color: var(--chrometext-light); }
.switch-link a { color: var(--chrome-blue); text-decoration: none; }
.badge {
display: inline-block;
padding: 3px 10px;
border-radius: 12px;
font-size: 12px;
font-weight: 600;
}
.badge-admin { background: #fce8e6; color: var(--chrome-red); }
.badge-user { background: #e8f0fe; color: var(--chrome-blue); }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { text-align: left; padding: 10px; border-bottom: 1px solid var(--chrome-border);
font-size: 14px; }
th { color: var(--chrome-text-light); font-weight: 600; }
</style>
</head>
<body>
<div class="browser-bar">
<div class="browser-dots">
<span class="dot-red"></span><span class="dot-yellow"></span><span class="dotgreen"></span>
</div>
<div class="address-bar">👋 localhost/myapp</div>
</div>
<nav class="topnav">
<div class="brand">The MediX</div>
<div class="links">
<?php if (!empty($_SESSION['user_id'])): ?>
<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
<a href="index.php?action=admin">Admin panel</a>
<?php else: ?>
<a href="index.php?action=dashboard">My space</a>
<?php endif; ?>
<a href="index.php?action=logout" class="btn-primary">Log out</a>
<?php else: ?>
<a href="index.php?action=login">Log in</a>
<a href="index.php?action=register" class="btn-primary">Sign up</a>
<?php endif; ?>
</div>
</nav>
<?= $content ?>
</body>
</html>