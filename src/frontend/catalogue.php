<?php
require '../utils/gestionConnexion.php';
$pdo = Connexion::getConnexion();

$search = isset($_GET['recherche']) ? $_GET['recherche'] : '';


$sql = "SELECT * FROM Materiel WHERE nomMateriel LIKE :search OR typeMateriel LIKE :search";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue - Emprunts Fablab</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       
        .info-tooltip {
            font: normal;
            display: inline-block;
            margin-left: 8px;
            color: #666;
            cursor: help;
            position: relative;
        }
        
        .info-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            width: 200px;
            z-index: 100;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
       
        .info-tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 110%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="app-header small-header">
            <a href="index.php" class="back-link">&larr; Retour à l'accueil</a>
            <h2 style="color:white; text-align:center;">Réserver du matériel</h2>
        </header>

        <form action="traitement_reservation.php" method="POST" class="reservation-form">
            <div class="form-section">
                <h2>Vos informations</h2>
                <div class="form-group">
                    <label>Email :</label>
                    <input type="email" name="email" required class="form-input" placeholder="votre@email.com">
                </div>
                <div class="form-group">
                    <label>Motif de l'emprunt :</label>
                    <textarea name="motif" required class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Date début :</label>
                    <input type="date" name="date_debut" id="date_from" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Date fin (prévue) :</label>
                    <input type="date" name="date_fin" id="date_to" required class="form-input">
                </div>
            </div>

            <div class="form-section">
                <h2>Matériel disponible</h2>
                
                <input type="text" id="search" class="search-input" placeholder="Filtrer la liste...">

                <div class="materials-list">
                    <?php if (empty($materiels)): ?>
                        <p>Aucun matériel trouvé.</p>
                    <?php else: ?>
                        <?php foreach ($materiels as $m): ?>
                            <div class="material-item">
                                <label class="material-label <?php echo ($m['stockDisponible'] <= 0) ? 'disabled' : ''; ?>">
                                    <input type="checkbox" name="materiel[<?php echo $m['idMateriel']; ?>]" 
                                           class="material-checkbox" 
                                           <?php echo ($m['stockDisponible'] <= 0) ? 'disabled' : ''; ?>>
                                    
                                    <div class="material-info">
                                        <h3>
                                            <?php echo htmlspecialchars($m['nomMateriel']); ?>
                                            <i class="fas fa-question-circle info-tooltip" 
                                               data-tooltip="<?php echo htmlspecialchars($m['descriptionMateriel']); ?>"></i>
                                        </h3>
                                        <p>Type: <?php echo htmlspecialchars($m['typeMateriel']); ?></p>
                                        <small>Stock dispo: <?php echo $m['stockDisponible']; ?> / <?php echo $m['stockTotal']; ?></small>
                                    </div>
                                </label>
                                
                                <input type="number" name="quantite[<?php echo $m['idMateriel']; ?>]" 
                                       class="quantity-input" min="1" 
                                       max="<?php echo $m['stockDisponible']; ?>" 
                                       placeholder="Qté" disabled>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="submit-btn">Confirmer la réservation</button>
            </div>
        </form>
    </div>
    <script src="reservation.js"></script>
</body>
</html>