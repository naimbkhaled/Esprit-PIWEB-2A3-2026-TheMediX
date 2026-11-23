<?php
// =============================================================================
// HealthConnect - Configuration de la Base de Données (PDO Singleton)
// =============================================================================

// Paramètres de connexion MySQL (XAMPP par défaut)
define('DB_HOST', 'localhost');
define('DB_NAME', 'healthconnect');
define('DB_USER', 'root');
define('DB_PASS', '');
define('PER_PAGE', 5); // Nombre d'éléments par page pour la pagination

/**
 * Classe Config : Gère la connexion unique à la base de données (Design Pattern Singleton)
 */
class Config
{
    private static ?PDO $pdo = null;

    /**
     * Retourne l'instance unique de connexion PDO.
     * Si elle n'existe pas encore, elle est créée et configurée.
     *
     * @return PDO
     */
    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /**
     * Lit et récupère les informations de diagnostic de la base de données
     *
     * @return array Informations système et données de la base
     */
    public static function getDatabaseInfo(): array
    {
        $db = self::getConnexion();
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $stats = [
            'database' => DB_NAME,
            'host'     => DB_HOST,
            'user'     => DB_USER,
            'version'  => $db->query("SELECT VERSION()")->fetchColumn(),
            'tables'   => $tables,
            'counts'   => []
        ];

        foreach ($tables as $table) {
            $count = (int)$db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $stats['counts'][$table] = $count;
        }

        return $stats;
    }
}

/**
 * Fonction helper globale pour compatibilité avec le reste du projet MVC
 */
function getConnexion(): PDO
{
    return Config::getConnexion();
}

// -----------------------------------------------------------------------------
// Test direct de la connexion si le fichier config.php est appelé directement
// (ex: http://localhost/.../config.php ou en ligne de commande)
// -----------------------------------------------------------------------------
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    if (php_sapi_name() === 'cli') {
        try {
            $info = Config::getDatabaseInfo();
            echo "Connexion reussie a la base : " . $info['database'] . PHP_EOL;
            echo "Version MySQL : " . $info['version'] . PHP_EOL;
            echo "Tables (" . count($info['tables']) . ") : " . implode(', ', $info['tables']) . PHP_EOL;
            foreach ($info['counts'] as $t => $c) {
                echo " - Table '$t' : $c enregistrement(s)" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage() . PHP_EOL;
        }
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8">';
        echo '<title>Test Connexion Base de Données - HealthConnect</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '</head><body class="p-4 bg-light"><div class="container" style="max-width: 600px;">';
        echo '<div class="card shadow-sm">';
        echo '<div class="card-header bg-primary text-white"><h4 class="mb-0">Diagnostic Base de Données - HealthConnect</h4></div>';
        echo '<div class="card-body">';

        try {
            $info = Config::getDatabaseInfo();
            echo '<div class="alert alert-success d-flex align-items-center mb-3"><div><strong>Connexion réussie avec succès !</strong></div></div>';
            echo '<ul class="list-group mb-3">';
            echo '<li class="list-group-item d-flex justify-content-between"><span>Base de données :</span> <strong>' . htmlspecialchars($info['database']) . '</strong></li>';
            echo '<li class="list-group-item d-flex justify-content-between"><span>Hôte :</span> <strong>' . htmlspecialchars($info['host']) . '</strong></li>';
            echo '<li class="list-group-item d-flex justify-content-between"><span>Utilisateur :</span> <strong>' . htmlspecialchars($info['user']) . '</strong></li>';
            echo '<li class="list-group-item d-flex justify-content-between"><span>Version MySQL :</span> <strong>' . htmlspecialchars($info['version']) . '</strong></li>';
            echo '</ul>';
            echo '<h5>Tables & enregistrements :</h5><ul class="list-group mb-3">';
            foreach ($info['counts'] as $t => $c) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo '<span>' . htmlspecialchars($t) . '</span>';
                echo '<span class="badge bg-primary rounded-pill">' . $c . ' enregistrements</span>';
                echo '</li>';
            }
            echo '</ul>';
            echo '<a href="index.php" class="btn btn-outline-primary w-100">&larr; Retour à l\'application HealthConnect</a>';
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">';
            echo '<strong>Erreur lors de la connexion :</strong><br>' . htmlspecialchars($e->getMessage());
            echo '</div>';
            echo '<p class="small text-muted">Vérifiez que MySQL est bien démarré dans XAMPP et que la base "healthconnect" a été importée depuis <code>database.sql</code>.</p>';
        }

        echo '</div></div></div></body></html>';
    }
}
