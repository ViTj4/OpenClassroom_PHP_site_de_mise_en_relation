<?php

/**
 * Classe qui permet de se connecter à la base de données.
 * Cette classe est un singleton. Cela signifie qu'il n'est pas possible de créer plusieurs instances de cette classe.
 * Pour récupérer une instance de cette classe, il faut utiliser la méthode getInstance().
 */
class DBManager
{
    // Instance unique partagée par les managers du projet.
    private static ?DBManager $instance = null;

    /**
     * Constructeur de la classe DBManager.
     * Initialise la connexion à la base de données.
     * Ce constructeur est privé. Pour récupérer une instance de la classe, il faut utiliser la méthode getInstance().
     * @see DBManager::getInstance()
     */
    private function __construct(
        private readonly PDO $db
    ) {}

    /**
     * Méthode qui permet de récupérer l'instance de la classe DBManager.
     * @return DBManager
     */
    public static function getInstance(): DBManager
    {
        if (self::$instance === null) {
            self::$instance = new DBManager(self::createPDO());
        }
        return self::$instance;
    }

    private static function createPDO(): PDO
    {
        $host     = requiredEnv('DB_HOST');
        $database = requiredEnv('DB_NAME');
        $user     = requiredEnv('DB_USER');
        // Peut rester vide pour utilisation de XAMPP en local.
        $password = env('DB_PASS', '');

        $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8mb4', $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // On désactive l'émulation pour laisser MySQL gérer les vraies requêtes préparées.
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }

    /**
     * Méthode qui permet de récupérer l'objet PDO qui permet de se connecter à la base de données.
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->db;
    }

    /**
     * Méthode qui permet d'exécuter une requête SQL.
     * Toutes les requêtes passent par une requête préparée.
     * @param string $sql       : la requête SQL à exécuter.
     * @param array|null $params: les paramètres de la requête SQL.
     * @return PDOStatement     : le résultat de la requête SQL.
     */
    public function query(string $sql, ?array $params = null): PDOStatement
    {
        // Même sans paramètres, on prépare la requête pour garder un comportement uniforme et sûr.
        $query = $this->db->prepare($sql);

        if ($query === false) {
            throw new RuntimeException('Impossible de préparer la requête SQL.');
        }

        foreach ($params ?? [] as $name => $value) {
            // bindValue() associe une valeur concrète au placeholder SQL.
            // Les paramètres nommés (:email) et positionnels (?) sont tous les deux acceptés.
            $query->bindValue(
                is_int($name) ? $name + 1 : ':' . ltrim((string) $name, ':'),
                $value,
                $this->getParameterType($value)
            );
        }

        $query->execute();

        return $query;
    }

    private function getParameterType(mixed $value): int
    {
        // PDO a besoin du type de donnée pour binder correctement les valeurs.
        return match (true) {
            is_int($value)  => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            $value === null => PDO::PARAM_NULL,
            default         => PDO::PARAM_STR,
        };
    }
}
