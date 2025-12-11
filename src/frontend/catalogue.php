<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - Emprunts Fablab</title>
    <link rel="stylesheet" href="css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div class="page-container">

        <header class="app-header small-header">
            <a href="index.php" class="back-link">&larr; Retour à l'accueil</a>
            <h2>Commencer à emprunter du matériel :</h2>
        </header>

        <main class="catalogue-main">

            <form action="catalogue.php" method="GET" class="search-bar">
                <label for="search-input">Matériel :</label>
                <div class="search-field">
                    <input type="text" id="search-input" name="recherche" placeholder="Rechercher un materiel...">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <section class="material-list-container">
                <h3>Liste du matériel :</h3>

                <?php

                $recherche = isset($_GET['recherche']) ? strtolower($_GET['recherche']) : '';
                $materiel_factice = [
                    1 => "Oscilloscope Rigol",
                    2 => "Fer à souder",
                    3 => "Kit Arduino Uno",
                    4 => "Raspberry Pi 4",
                    5 => "Multimètre numérique",
                    6 => "Item 6",
                    7 => "Item 7",
                    8 => "Item 8",
                    9 => "Item 10",
                ];
                $resultats = [];
                if (empty($recherche)) {
                    $resultats = $materiel_factice;
                } else {
                    foreach ($materiel_factice as $id => $nom) {
                        if (stripos($nom, $recherche) !== false) {
                            $resultats[$id] = $nom;
                        }
                    }
                }
                ?>

                <ul class="material-list">
                    <?php 
                    if (empty($resultats)){ ?>
                        <li>Aucun matériel trouvé.</li>
                    <?php 
                    }else {
                            foreach ($resultats as $id => $nom) { ?>
                            <li>
                                <a href="materiel.php?id=<?php echo $id; ?>">
                                    <?php echo htmlspecialchars($nom); ?>
                                </a>
                            </li>
                    <?php   }
                    }?>
                </ul>
            </section>

        </main>

    </div>
</body>

</html>