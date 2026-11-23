<?php
ob_start();
?>

<!-- Hero section -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">🩺 Diabetes Care Platform</div>
        <h1>Manage your <span>diabetes journey</span><br>with confidence</h1>
        <p>Track your glucose levels, nutrition, medications and appointments — all in one secure, easy-to-use platform built for people living with diabetes.</p>
        <div class="hero-actions">
            <a href="index.php?action=register" class="btn btn-white">🚀 Get started free</a>
            <a href="index.php?action=login"    class="btn btn-outline-white">Log in</a>
        </div>
    </div>
</section>

<!-- Features section -->
<section class="features-section">
    <div class="section-label">Why Medix?</div>
    <h2 class="section-title">Everything you need to stay healthy</h2>
    <p class="section-sub">Medix gives you a complete toolkit to monitor and manage Type 1 &amp; Type 2 diabetes from day to day.</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📈</div>
            <h3>Glucose Tracking</h3>
            <p>Log and visualise your blood sugar readings with charts and trend analysis.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🥗</div>
            <h3>Nutrition Log</h3>
            <p>Monitor carbohydrate intake, meals and glycaemic impact in seconds.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💊</div>
            <h3>Medication Reminders</h3>
            <p>Never miss an insulin dose or pill with smart, personalised reminders.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Appointment Manager</h3>
            <p>Schedule and track consultations with your endocrinologist or dietitian.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>Health Reports</h3>
            <p>Generate PDF summaries to share with your healthcare team at every visit.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure & Private</h3>
            <p>Your health data is encrypted and only accessible by you.</p>
        </div>
    </div>
</section>

<!-- Modules row (inspired by the reference image) -->
<section class="modules-section">
    <div class="section-label">Modules</div>
    <h2 class="section-title">Your diabetes toolkit</h2>
    <p class="section-sub">Coming soon — click any module to explore (not yet active).</p>
    <div class="modules-grid">
        <div class="module-card m-blue">
            <span class="mod-icon">📊</span>
            <span class="mod-label">Glucose Dashboard</span>
        </div>
        <div class="module-card m-purple">
            <span class="mod-icon">💊</span>
            <span class="mod-label">Insulin & Meds</span>
        </div>
        <div class="module-card m-rose">
            <span class="mod-icon">🥗</span>
            <span class="mod-label">Nutrition Planner</span>
        </div>
        <div class="module-card m-orange">
            <span class="mod-icon">🏃</span>
            <span class="mod-label">Exercise Tracker</span>
        </div>
        <div class="module-card m-navy">
            <span class="mod-icon">📅</span>
            <span class="mod-label">Appointments</span>
        </div>
        <div class="module-card m-green">
            <span class="mod-icon">📋</span>
            <span class="mod-label">Health Reports</span>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title   = 'Medix – Diabetes Management Platform';
require __DIR__ . '/layout.php';
