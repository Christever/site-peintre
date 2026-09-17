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

$confirmationSujet = "Votre demande de devis - MS Peinture";

/*
 * Version texte brut
 * Utilisée par les messageries qui ne prennent pas en charge le HTML.
 */
$confirmationTexte = "Bonjour " . $prenom . ",\n\n";
$confirmationTexte .= "Nous avons bien reçu votre demande de devis.\n\n";
$confirmationTexte .= "Voici le message que vous nous avez transmis :\n\n";
$confirmationTexte .= $message . "\n\n";
$confirmationTexte .= "Nous reviendrons vers vous dans les meilleurs délais afin d'échanger sur votre projet.\n\n";
$confirmationTexte .= "Cordialement,\n\n";
$confirmationTexte .= "MS PEINTURE\n";
$confirmationTexte .= "Mickael STEVERLYNCK\n";
$confirmationTexte .= "07 87 98 20 71\n";
$confirmationTexte .= "mspeinture-contact@steverlynck.fr\n";
$confirmationTexte .= "Bédarieux et alentours";

/*
 * Version HTML
 */
$confirmationHtml = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre demande de devis - MS Peinture</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f5f4; font-family:Arial, Helvetica, sans-serif; color:#292524;">

    <div style="max-width:600px; margin:0 auto; padding:30px 15px;">

        <div style="background-color:#1c1917; padding:25px; text-align:center;">
            <img
                src="https://www.steverlynck.fr/mspeinture/logo-ms-peinture.jpg"
                alt="MS Peinture"
                style="display:block; max-width:180px; height:auto; margin:0 auto;"
            >
        </div>

        <div style="background-color:#ffffff; padding:35px 30px;">

            <p style="margin:0 0 8px; color:#c2410c; font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">
                Demande de devis
            </p>

            <h1 style="margin:0 0 25px; color:#1c1917; font-size:26px;">
                Votre demande a bien été reçue
            </h1>

            <p style="font-size:16px; line-height:1.7;">
                Bonjour ' . htmlspecialchars($prenom, ENT_QUOTES, "UTF-8") . ',
            </p>

            <p style="font-size:16px; line-height:1.7;">
                Nous avons bien reçu votre demande de devis et vous en remercions.
            </p>

            <p style="font-size:16px; line-height:1.7;">
                Voici le message que vous nous avez transmis :
            </p>

            <div style="margin:25px 0; padding:20px; background-color:#fafaf9; border-left:4px solid #c2410c;">
                <p style="margin:0; white-space:pre-wrap; font-size:15px; line-height:1.7;">
                    ' . nl2br(htmlspecialchars($message, ENT_QUOTES, "UTF-8")) . '
                </p>
            </div>

            <p style="font-size:16px; line-height:1.7;">
                Nous reviendrons vers vous dans les meilleurs délais afin d’échanger sur votre projet.
            </p>

            <p style="margin-top:30px; font-size:16px; line-height:1.7;">
                Cordialement,
            </p>

            <p style="margin:0; font-size:16px; line-height:1.7;">
                <strong>MS PEINTURE</strong><br>
                Mickael STEVERLYNCK
            </p>

            <p style="margin-top:20px; font-size:14px; line-height:1.7; color:#57534e;">
                07 87 98 20 71<br>
                mspeinture-contact@steverlynck.fr<br>
                Bédarieux et alentours
            </p>

        </div>

        <div style="padding:20px; text-align:center; color:#78716c; font-size:12px;">
            MS PEINTURE · Bédarieux et alentours
        </div>

    </div>

</body>
</html>
';

$confirmationHeaders = [];
$confirmationHeaders[] = "From: MS Peinture <mspeinture-contact@steverlynck.fr>";
$confirmationHeaders[] = "MIME-Version: 1.0";
$confirmationHeaders[] = 'Content-Type: multipart/alternative; boundary="MSPEINTURE_BOUNDARY"';

$confirmationBody = "--MSPEINTURE_BOUNDARY\r\n";
$confirmationBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
$confirmationBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$confirmationBody .= $confirmationTexte . "\r\n\r\n";

$confirmationBody .= "--MSPEINTURE_BOUNDARY\r\n";
$confirmationBody .= "Content-Type: text/html; charset=UTF-8\r\n";
$confirmationBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$confirmationBody .= $confirmationHtml . "\r\n\r\n";

$confirmationBody .= "--MSPEINTURE_BOUNDARY--";

mail(
    $email,
    $confirmationSujet,
    $confirmationBody,
    implode("\r\n", $confirmationHeaders)
);

header("Location: /mspeinture/merci/");
exit;