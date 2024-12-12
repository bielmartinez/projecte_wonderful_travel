<?php
//Funcio per conectar amb la BD mitjançant PDO
function conectarBD()
{
    $connexio = new PDO('mysql:host=localhost;dbname=wonderful_travel', 'root', '');
    return $connexio;
}

//Funcio per evitar injeccio de codi als formularis
function arreglarDades($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

function selectContinent()
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('SELECT DISTINCT continent FROM destins');
    $consulta->execute();
    $continent = $consulta->fetchAll(PDO::FETCH_ASSOC);
    return $continent;
}

function selectPais($continent)
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('SELECT pais FROM destins WHERE continent = :continent');
    $consulta->bindParam(':continent', $continent, PDO::PARAM_STR);
    $consulta->execute();
    $pais = $consulta->fetchAll(PDO::FETCH_ASSOC);
    return $pais;
}

function selectPreu($pais)
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('SELECT preu FROM destins WHERE pais = :pais');
    $consulta->bindParam(':pais', $pais, PDO::PARAM_STR);
    $consulta->execute();
    $preu = $consulta->fetch(PDO::FETCH_ASSOC);
    return $preu;
}

function selectImatge($pais)
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('SELECT foto FROM destins WHERE pais = :pais');
    $consulta->bindParam(':pais', $pais, PDO::PARAM_STR);
    $consulta->execute();
    $imatge = $consulta->fetch(PDO::FETCH_ASSOC);
    return $imatge;
}

function selectIdDesti($pais)
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('SELECT id FROM destins WHERE pais = :pais');
    $consulta->bindParam(':pais', $pais, PDO::PARAM_STR);
    $consulta->execute();
    $desti = $consulta->fetch(PDO::FETCH_ASSOC);
    
    if ($desti === false) {
        error_log("No se encontró el id para el país: " . $pais);
        return null;
    }
    
    return $desti['id'];
}

function insertarReserva($id_desti, $fecha_reserva, $nom_titular, $telefon, $persones)
{
    $connexio = conectarBD();
    $consulta = $connexio->prepare('INSERT INTO reserva (id_desti, fecha_reserva, nom_titular, telefon, persones) VALUES (:id_desti, :fecha_reserva, :nom_titular, :telefon, :persones)');
    $consulta->bindParam(':id_desti', $id_desti, PDO::PARAM_INT);
    $consulta->bindParam(':fecha_reserva', $fecha_reserva, PDO::PARAM_STR);
    $consulta->bindParam(':nom_titular', $nom_titular, PDO::PARAM_STR);
    $consulta->bindParam(':telefon', $telefon, PDO::PARAM_STR);
    $consulta->bindParam(':persones', $persones, PDO::PARAM_INT);
    $consulta->execute();
}

if (isset($_GET['continent'])) {
    $continent = arreglarDades($_GET['continent']);
    $paisos = selectPais($continent);
    echo json_encode($paisos);
}

if (isset($_GET['pais'])) {
    $pais = arreglarDades($_GET['pais']);
    $preu = selectPreu($pais);
    $imatge = selectImatge($pais);
    echo json_encode(['preu' => $preu['preu'], 'imatge' => $imatge['foto']]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pais']) && isset($_POST['dataViatge']) && isset($_POST['nomTitular']) && isset($_POST['telefon']) && isset($_POST['persones'])) {
    $pais = arreglarDades($_POST['pais']);
    $dataViatge = arreglarDades($_POST['dataViatge']);
    $nomTitular = arreglarDades($_POST['nomTitular']);
    $telefon = arreglarDades($_POST['telefon']);
    $persones = arreglarDades($_POST['persones']);
    
    error_log("Datos recibidos: pais=$pais, dataViatge=$dataViatge, nomTitular=$nomTitular, telefon=$telefon, persones=$persones");
    
    $id_desti = selectIdDesti($pais);
    
    if ($id_desti === null) {
        error_log("Error: id_desti es null para el país: " . $pais);
    } else {
        insertarReserva($id_desti, $dataViatge, $nomTitular, $telefon, $persones);
    }
}
?>