<?php
require_once 'db.php'; // On prend notre téléphone pour parler au placard (la base de données)

// 1. ON GÈRE LA RECHERCHE
// On vérifie si l'utilisateur a écrit quelque chose dans la barre de recherche
if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {
    $mot = $_GET['recherche']; // On lit le mot qu'il a tapé
    $mot_magique = '%' . $mot . '%'; // On ajoute des '%' pour dire "trouve n'importe quel livre qui CONTIENT ce mot"
    
    // On prépare une question spéciale : "Donne-moi les livres dont le titre OU l'auteur ressemble au mot magique"
    $stmt = $pdo->prepare("SELECT * FROM livres WHERE titre LIKE ? OR auteur LIKE ? ORDER BY id DESC");
    $stmt->execute([$mot_magique, $mot_magique]); // On pose la question
    $livres = $stmt->fetchAll(); // On récupère seulement les livres trouvés
} else {
    // Si on n'a rien cherché, on demande au placard de nous donner TOUS les livres, du plus récent au plus ancien
    $livres = $pdo->query("SELECT * FROM livres ORDER BY id DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Bibliothèque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; } /* On peint le mur de fond en gris très clair */
        .card { border: none; border-radius: 15px; } /* On adoucit les bords de nos boîtes pour qu'elles soient rondes */
        .btn-anthracite { background-color: #2c3e50; color: white; } /* On invente un bouton gris foncé très classe */
        .btn-anthracite:hover { background-color: #1a252f; color: white; } /* Quand on touche le bouton, il devient un peu plus sombre */
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-5 fw-bold" style="color: #2c3e50;">
        <i class="fa-solid fa-book"></i> MA BIBLIOTHÈQUE
    </h2>

    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h5 class="fw-bold mb-3">Nouveau Livre</h5>
                <form action="actions.php" method="POST">
                    <input type="hidden" name="action" value="ajouter"> <div class="mb-3">
                        <label class="small fw-bold">Titre</label>
                        <input type="text" name="titre" class="form-control" placeholder="Le nom du livre" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Auteur</label>
                        <input type="text" name="auteur" class="form-control" placeholder="Qui l'a écrit ?" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Année</label>
                        <input type="number" name="annee" class="form-control" placeholder="Ex: 2023" required>
                    </div>
                    
                    <button type="submit" class="btn btn-anthracite w-100">Ajouter</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            
            <form action="index.php" method="GET" class="mb-4">
                <div class="input-group shadow-sm">
                    <input type="text" name="recherche" class="form-control border-0 p-3" placeholder="Chercher un titre ou un auteur..." value="<?php echo isset($_GET['recherche']) ? htmlspecialchars($_GET['recherche']) : ''; ?>">
                    <button type="submit" class="btn btn-anthracite px-4">
                        <i class="fa-solid fa-magnifying-glass"></i> Chercher
                    </button>
                    <?php if(isset($_GET['recherche']) && !empty($_GET['recherche'])): ?>
                        <a href="index.php" class="btn btn-light px-4 border text-danger" title="Annuler la recherche">
                            <i class="fa-solid fa-xmark mt-2"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="card shadow-sm p-4 text-center">
                <h5 class="fw-bold mb-4 text-start">Inventaire</h5>
                
                <table class="table align-middle">
                    <thead>
                        <tr class="table-light">
                            <th>Livre</th>
                            <th>Année</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($livres)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucun livre trouvé.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($livres as $l): ?>
                        <tr>
                            <td class="text-start">
                                <strong><?php echo htmlspecialchars($l['titre']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($l['auteur']); ?></small>
                            </td>
                            <td><?php echo $l['annee_publication']; ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $l['statut'] == 'disponible' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $l['statut']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    
                                    <form action="actions.php" method="POST">
                                        <input type="hidden" name="action" value="toggle_statut">
                                        <input type="hidden" name="id" value="<?php echo $l['id']; ?>">
                                        <input type="hidden" name="actuel" value="<?php echo $l['statut']; ?>">
                                        <button class="btn btn-sm btn-outline-secondary border-0" title="Changer statut"><i class="fa-solid fa-retweet"></i></button>
                                    </form>

                                    <button class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#edit<?php echo $l['id']; ?>" title="Modifier"><i class="fa-solid fa-pen"></i></button>

                                    <form action="actions.php" method="POST" onsubmit="return confirm('Es-tu sûr de vouloir jeter ce livre à la poubelle ?')">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?php echo $l['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger border-0" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit<?php echo $l['id']; ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="actions.php" method="POST">
                                <div class="modal-header">
                                    <h5>Modifier le livre</h5>
                                </div>
                                <div class="modal-body text-start">
                                    <input type="hidden" name="action" value="modifier">
                                    <input type="hidden" name="id" value="<?php echo $l['id']; ?>">
                                    <div class="mb-3">
                                        <label>Titre</label>
                                        <input type="text" name="titre" value="<?php echo htmlspecialchars($l['titre']); ?>" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Auteur</label>
                                        <input type="text" name="auteur" value="<?php echo htmlspecialchars($l['auteur']); ?>" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Année</label>
                                        <input type="number" name="annee" value="<?php echo htmlspecialchars($l['annee_publication']); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; // On a fini avec ce livre, on passe au suivant ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>