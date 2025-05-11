<?php
header("Access-Control-Allow-Origin: *"); // Autorise toutes les origines (à des fins de développement)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Autorise les méthodes courantes
header("Access-Control-Allow-Headers: Content-Type"); // Autorise l'en-tête Content-Type

// Informations de connexion à la base de données
$User = "projectiot";
$Passwd="P@ssword";
$dsn="mysql:host=localhost;dbname=projectiot";

// Vérifier si la connexion a réussi
try{
    $db = new PDO($dsn,$User,$Passwd);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer la gestion des erreurs PDO
    } catch(PDOException $e){
    die("<P> La base de données n'est pas accessible ! Erreur : " . $e->getMessage() . "</p>");
    }

// Vérifier si des données ont été envoyées par l'ESP8266
if (isset($_GET['humidite']) && isset($_GET['temperature'])) {
    $humiditeData = [
        'type_capteur' => 2 ,
        'mesure' => $_GET['humidite']
    ];

    $temperatureData = [
        'type_capteur' => 1 ,
        'mesure' => $_GET['temperature']
    ];

    // Préparer la requête pour l'humidité
    $stmtHumidite = $db->prepare("INSERT INTO capteur (type_capteur, mesure) VALUES (:type_capteur, :mesure)");
    try {
        $stmtHumidite->execute($humiditeData);
        echo "Données d'humidité insérées avec succès.\n";
    } catch (PDOException $e) {
        echo "Erreur lors de l'insertion des données d'humidité : " . $e->getMessage() . "\n";
    }

    // Préparer la requête pour la température
    $stmtTemperature = $db->prepare("INSERT INTO capteur (type_capteur, mesure) VALUES (:type_capteur, :mesure)");
    try {
        $stmtTemperature->execute($temperatureData);
        echo "Données de température insérées avec succès.\n";
    } catch (PDOException $e) {
        echo "Erreur lors de l'insertion des données de température : " . $e->getMessage() . "\n";
    }
} else {
    echo "Aucune donnée d'humidité ou de température reçue.\n";
}

$db = null; // Fermer la connexion PDO
?>