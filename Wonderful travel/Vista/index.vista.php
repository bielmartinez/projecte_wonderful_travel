<!doctype html>
<html lang="ca">
<head>
    <title>Wonderful Travel</title>
    <meta charset="UTF-8" />   
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./Vista/Estils/vista_rellotje_analogic.css">
    <link rel="stylesheet" href="./Vista/Estils/estil.css">
    <script defer src="./Model/rellotge.js"></script>
</head>
<body onload="init(); setDefaultDate()">
    <div class="container">
        <header>Wonderful travel</header>
        <canvas id="canvas" width="200" height="200"></canvas>
        <form id="viatgeForm" method="POST">
            <div class="form-group">
                <label for="continent">Continent:</label>
                <select id="continent" name="continent" class="form-control">
                    <option value="">Selecciona un continent</option>
                    <?php foreach ($continents as $continent): ?>
                        <option value="<?= $continent['continent'] ?>" <?= isset($_COOKIE['selectedContinent']) && $_COOKIE['selectedContinent'] == $continent['continent'] ? 'selected' : '' ?>><?= $continent['continent'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pais">País:</label>
                <select id="pais" name="pais" class="form-control">
                    <option value="">Selecciona un país</option>
                    <?php foreach ($paisos as $pais): ?>
                        <option value="<?= $pais['pais'] ?>"><?= $pais['pais'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="dataViatge">Data del viatge:</label>
                <input type="date" id="dataViatge" name="dataViatge" class="form-control">
            </div>
            <div class="form-group">
                <label for="nomTitular">Nom del titular:</label>
                <input type="text" id="nomTitular" name="nomTitular" class="form-control">
            </div>
            <div class="form-group">
                <label for="telefon">Telèfon:</label>
                <input type="text" id="telefon" name="telefon" class="form-control">
            </div>
            <div class="form-group">
                <label for="persones">Persones:</label>
                <input type="number" id="persones" name="persones" class="form-control" value="1" min="1">
            </div>
            <div class="form-group">
                <label for="preu">Preu:</label>
                <input type="text" id="preu" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="imatge">Imatge:</label>
                <img id="imatge" src="./Vista/img/placeholder.webp" alt="Imatge del país" class="img-fluid">
            </div>
            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>
        <div id="reserves" class="mt-3 d-flex flex-wrap">
    <!-- Llista de reserves -->
    <?php
    $reserves = obtenirReserves();
    foreach ($reserves as $reserva): 
        $imatge_path = "./Vista/img/{$reserva['pais']}.jpg";
        // Verificar si la imagen existe
        if (!file_exists($imatge_path)) {
            $imatge_path = "./Vista/img/placeholder.webp";
        }
    ?>
        <div class="card m-2" style="width: 18rem;">
            <img src="<?= $imatge_path ?>" class="card-img-top" alt="<?= $reserva['pais'] ?>">
            <div class="card-body">
                <h5 class="card-title"><?= $reserva['pais'] ?></h5>
                <p class="card-text">
                    <strong>Data:</strong> <?= $reserva['fecha_reserva'] ?><br>
                    <strong>Nom:</strong> <?= $reserva['nom_titular'] ?><br>
                    <strong>Telèfon:</strong> <?= $reserva['telefon'] ?><br>
                    <strong>Persones:</strong> <?= $reserva['persones'] ?><br>
                    <strong>Preu:</strong> <?= number_format($reserva['preu'] * $reserva['persones'], 2) ?> €
                </p>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="id" value="<?= $reserva['id'] ?>">
                    <input type="hidden" name="eliminarReserva" value="1">
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    <script>
    let preuBase = 0;

    //Funció per fer que la data per defecte sigui la d'avui
    function setDefaultDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dataViatge').value = today;
    }
    
    //Funció per obtenir els països del continent seleccionat
    document.getElementById('continent').addEventListener('change', function() {
        document.cookie = "selectedContinent=" + this.value + "; path=/";
        this.form.submit();
    });

    //Funció per obtenir el preu i la imatge del país seleccionat
    document.getElementById('pais').addEventListener('change', function() {
        const pais = this.value;
        fetch(`./Controlador/controlador.php?pais=${pais}`)
            .then(response => response.json())
            .then(data => {
                preuBase = parseFloat(data.preu); // Guardar el preu base
                document.getElementById('preu').value = preuBase.toFixed(2);
                const imatgeSrc = data.imatge ? data.imatge : './Vista/img/placeholder.webp';
                document.getElementById('imatge').src = imatgeSrc;
                updatePreu();
            });
    });

    //Funció per actualitzar el preu en funció del nombre de persones
    document.getElementById('persones').addEventListener('input', updatePreu);

    function updatePreu() {
        const persones = parseInt(document.getElementById('persones').value);
        if (!isNaN(preuBase) && !isNaN(persones)) {
            document.getElementById('preu').value = (preuBase * persones).toFixed(2);
        }
    }

    //Funció per recarrregar la pàgina si s'ha eliminat una reserva
    if (window.location.search.includes('eliminado=1')) {
        window.location.href = window.location.pathname;
    }
</script>
</body>
</html>

<?php
// Funció per obtenir el següent ID disponible com el més alt + 1
function obtenirIDMinim($pdo) {
    // Obtenir el ID més alt existent
    $sql = "SELECT IFNULL(MAX(id), 0) AS max_id FROM reserva";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $row['max_id'] + 1; // Retorna el següent ID com el màxim ID + 1
}

// Funció per reajustar els IDs de les reserves
function reajustarIDs($pdo) {
    // Obtenir totes les reserves ordenades per ID
    $sql = "SELECT * FROM reserva ORDER BY id";
    $stmt = $pdo->query($sql);
    $reserves = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Reassignar els IDs de forma consecutiva
    foreach ($reserves as $index => $reserva) {
        $newID = $index + 1; // Nou ID consecutiu
        
        // Actualitzar el ID només si és diferent
        if ($reserva['id'] != $newID) {
            $updateSql = "UPDATE reserva SET id = :newID WHERE id = :oldID";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(':newID', $newID);
            $updateStmt->bindParam(':oldID', $reserva['id']);
            $updateStmt->execute();
        }
    }
}

// Funció per eliminar una reserva
function eliminarReserva($pdo, $id) {
    $sql = "DELETE FROM reserva WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function obtenirReserves() {
    $connexio = conectarBD();
    $consulta = $connexio->prepare('
        SELECT r.id, r.fecha_reserva, r.nom_titular, r.telefon, r.persones, d.pais, d.preu 
        FROM reserva r 
        JOIN destins d ON r.id_desti = d.id
    ');
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['eliminarReserva'])) {
    $id = arreglarDades($_POST['id']);
    eliminarReserva(conectarBD(), $id);
    reajustarIDs(conectarBD());
    echo "<script>window.location.href = window.location.pathname + '?eliminado=1';</script>";
    exit;
}
?>