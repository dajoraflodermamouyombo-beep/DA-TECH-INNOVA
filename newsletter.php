<?php
require_once(__DIR__ . '/vendor/autoload.php');

use \SendinBlue\Client\Api\ContactsApi;
use \SendinBlue\Client\Configuration;
use \SendinBlue\Client\Model\CreateContact;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // --- 1. Vérification doublon CSV ---
        $file = 'subscribers.csv';
        if (!file_exists($file)) {
            file_put_contents($file, "email,date\n");
        }
        $existingEmails = array_map('str_getcsv', file($file));
        $emailsOnly = array_column($existingEmails, 0);

        if (in_array($email, $emailsOnly)) {
            echo "<p style='color:orange; text-align:center;'>Cet email est déjà abonné.</p>";
            exit;
        }

        // --- 2. Ajouter à Brevo ---
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
        $apiInstance = new ContactsApi(new GuzzleHttp\Client(), $config);
        $createContact = new CreateContact(['email' => $email, 'listIds' => [2]]);

        try {
            $apiInstance->createContact($createContact);

            // --- 3. Stockage CSV ---
            file_put_contents($file, "$email," . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

            // --- 4. Notification ---
            mail("datech.innova@gmail.com", "Nouvel abonné Newsletter", "Nouvel abonné : $email");

            echo "<p style='color:green; text-align:center;'>Merci ! Votre email a été ajouté à la newsletter.</p>";

        } catch (Exception $e) {
            echo "<p style='color:red; text-align:center;'>Erreur Brevo : " . $e->getMessage() . "</p>";
        }

    } else {
        echo "<p style='color:red; text-align:center;'>Email invalide.</p>";
    }
}
?>
