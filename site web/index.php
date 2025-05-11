<?php
$User = "projectiot";
$Passwd="P@ssword";
$dsn="mysql:host=localhost;dbname=projectiot";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
try {
    $db = new PDO($dsn, $User, $Passwd);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT timestamp, mesure, type_capteur FROM capteur ORDER BY timestamp DESC LIMIT 50");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $temperatureData = [];
    $humidityData = [];

    foreach ($results as $row) {
        if ($row['type_capteur'] == 1) {
            $temperatureData[] = ['timestamp' => $row['timestamp'], 'value' => $row['mesure']];
        } elseif ($row['type_capteur'] == 2) {
            $humidityData[] = ['timestamp' => $row['timestamp'], 'value' => $row['mesure']];
        }
    }
    $data = [
        'temperature' => array_reverse($temperatureData),
        'humidity' => array_reverse($humidityData)
    ];

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}$db = null;
?>