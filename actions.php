<?php
require_once 'db.php'; // On prend le téléphone qu'on a préparé avant

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Si quelqu'un nous a envoyé un message (un formulaire)
    
    if (isset($_POST['action']) && $_POST['action'] === 'ajouter') { // Si le message dit "AJOUTER"
        $titre = $_POST['titre']; // On lit le titre écrit sur le papier
        $auteur = $_POST['auteur']; // On lit l'auteur écrit sur le papier
        $annee = $_POST['annee']; // On lit l'année écrite sur le papier
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, annee_publication) VALUES (?, ?, ?)"); // On prépare une place dans le placard
        $stmt->execute([$titre, $auteur, $annee]); // On range les trois infos dans les cases
    } 

    if (isset($_POST['action']) && $_POST['action'] === 'modifier') { // Si le message dit "MODIFIER"
        $id = $_POST['id']; // On regarde quel livre on veut changer (son numéro)
        $titre = $_POST['titre']; // On prend le nouveau titre
        $auteur = $_POST['auteur']; // On prend le nouvel auteur
        $annee = $_POST['annee']; // On prend la nouvelle année
        $stmt = $pdo->prepare("UPDATE livres SET titre = ?, auteur = ?, annee_publication = ? WHERE id = ?"); // On dit au placard de changer les infos de ce livre précis
        $stmt->execute([$titre, $auteur, $annee, $id]); // On fait le changement pour de vrai
    }

    if (isset($_POST['action']) && $_POST['action'] === 'toggle_statut') { // Si le message dit "CHANGER STATUT"
        $id = $_POST['id']; // On regarde le numéro du livre
        $nouveau = ($_POST['actuel'] === 'disponible') ? 'emprunte' : 'disponible'; // Si c'était là, on dit que c'est prêté (et inversement)
        $stmt = $pdo->prepare("UPDATE livres SET statut = ? WHERE id = ?"); // On prépare le changement
        $stmt->execute([$nouveau, $id]); // On tourne l'interrupteur
    }

    if (isset($_POST['action']) && $_POST['action'] === 'supprimer') { // Si le message dit "SUPPRIMER"
        $stmt = $pdo->prepare("DELETE FROM livres WHERE id = ?"); // On prépare la poubelle pour ce numéro
        $stmt->execute([$_POST['id']]); // On jette le livre du placard
    }
}

header('Location: index.php'); // Une fois fini, on retourne vite voir la liste des livres
exit; // On arrête de travailler pour l'instant
?>