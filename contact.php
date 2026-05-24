
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use \SendinBlue\Client\Api\TransactionalEmailsApi;
use \SendinBlue\Client\Configuration;
use \SendinBlue\Client\Model\SendSmtpEmail;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Nettoyage et validation ---
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"] ?? ''));
    $message = strip_tags(trim($_POST["message"] ?? ''));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "<p style='color:red; text-align:center;'>Veuillez remplir tous les champs.</p>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red; text-align:center;'>Email invalide.</p>";
        exit;
    }

    // --- 1. Stockage CSV ---
    $file = 'messages_contact.csv';
    if (!file_exists($file)) {
        file_put_contents($file, "name,email,subject,message,date\n");
    }
    fputcsv(fopen($file, 'a'), [$name, $email, $subject, $message, date('Y-m-d H:i:s')]);

    // --- 2. Notification admin (PHP mail) ---
    $to = "datech.innova@gmail.com";
    $body = "Nom: $name\nEmail: $email\nSujet: $subject\nMessage:\n$message";
    $headers = "From: $email";

    if (mail($to, "Nouveau message de votre site: $subject", $body, $headers)) {
        $phpMailStatus = true;
    } else {
        $phpMailStatus = false;
    }

    // --- 3. Envoi via Brevo ---
    try {
        $config = Configuration::getDefaultConfiguration()->setApiKey(
            'api-key', 
            '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'
        );

        $apiInstance = new TransactionalEmailsApi(new GuzzleHttp\Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => "Nouveau message de $name : $subject",
            'sender' => ['name' => $name, 'email' => $email],
            'to' => [['email' => 'datech.innova@gmail.com', 'name' => 'DATECH INNOVA']],
            'htmlContent' => "<p><strong>Nom :</strong> $name</p>
                              <p><strong>Email :</strong> $email</p>
                              <p><strong>Sujet :</strong> $subject</p>
                              <p><strong>Message :</strong><br>$message</p>"
        ]);

        $apiInstance->sendTransacEmail($sendSmtpEmail);
        $brevoStatus = true;

    } catch (Exception $e) {
        $brevoStatus = false;
        $brevoError = $e->getMessage();
    }

    // --- 4. Message final à l'utilisateur ---
    if ($phpMailStatus || $brevoStatus) {
        echo "<p style='color:green; text-align:center;'>Merci ! Votre message a été envoyé avec succès.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Désolé, une erreur s'est produite lors de l'envoi. " . ($brevoError ?? '') . "</p>";
    }
}
?>
