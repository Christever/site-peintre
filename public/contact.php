<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Méthode non autorisée.");
}

$nom = trim($_POST["nom"] ?? "");
$prenom = trim($_POST["prenom"] ?? "");
$email = trim($_POST["email"] ?? "");
$telephone = trim($_POST["telephone"] ?? "");
$message = trim($_POST["message"] ?? "");
$website = trim($_POST["website"] ?? "");

if ($website !== "") {
    http_response_code(400);
    exit("Requête invalide.");
}

// Validation
if ($nom === "" || $prenom === "" || $email === "" || $message === "") {
    http_response_code(400);
    exit("Veuillez remplir tous les champs obligatoires.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit("Adresse email invalide.");
}

if (mb_strlen($nom) > 100 || mb_strlen($prenom) > 100) {
    http_response_code(400);
    exit("Nom ou prénom trop long.");
}

if (mb_strlen($email) > 254) {
    http_response_code(400);
    exit("Adresse email trop longue.");
}

if (mb_strlen($telephone) > 30) {
    http_response_code(400);
    exit("Numéro de téléphone trop long.");
}

if (mb_strlen($message) > 5000) {
    http_response_code(400);
    exit("Message trop long.");
}

$destinataire = "mspeinture-contact@steverlynck.fr";
$sujet = "Nouvelle demande de contact - MS Peinture";

$contenu = "Nouvelle demande de contact\n\n";
$contenu .= "Nom : " . $nom . "\n";
$contenu .= "Prénom : " . $prenom . "\n";
$contenu .= "Email : " . $email . "\n";
$contenu .= "Téléphone : " . ($telephone ?: "Non renseigné") . "\n\n";
$contenu .= "Message :\n";
$contenu .= $message . "\n";

$headers = [];
$headers[] = "From: MS Peinture <mspeinture-contact@steverlynck.fr>";
$headers[] = "Reply-To: " . $email;
$headers[] = "Content-Type: text/plain; charset=UTF-8";

$envoye = mail(
    $destinataire,
    $sujet,
    $contenu,
    implode("\r\n", $headers)
);

if (!$envoye) {
    http_response_code(500);
    exit("Une erreur est survenue lors de l'envoi du message.");
}

$confirmationSujet = "Votre demande de contact - MS Peinture";

$confirmationMessage = "Bonjour " . $prenom . ",\n\n";
$confirmationMessage .= "Nous avons bien reçu votre demande de contact.\n\n";
$confirmationMessage .= "L'équipe MS Peinture reviendra vers vous dès que possible.\n\n";
$confirmationMessage .= "Cordialement,\n";
$confirmationMessage .= "MS Peinture\n";
$confirmationMessage .= "Bédarieux et alentours";

$confirmationHeaders = [];
$confirmationHeaders[] = "From: MS Peinture <mspeinture-contact@steverlynck.fr>";
$confirmationHeaders[] = "Content-Type: text/plain; charset=UTF-8";

mail(
    $email,
    $confirmationSujet,
    $confirmationMessage,
    implode("\r\n", $confirmationHeaders)
);

header("Location: /mspeinture/merci/");
exit;