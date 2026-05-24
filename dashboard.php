<?php
// ------------------------------
// DASHBOARD DATECH INNOVA
// Affichage abonnés newsletter + messages contact
// ------------------------------

// --- 1. LECTURE ABONNÉS NEWSLETTER ---
$subscribersFile = 'subscribers.csv';
$subscribers = [];

if (file_exists($subscribersFile)) {
    $subscribers = array_map('str_getcsv', file($subscribersFile));
    array_shift($subscribers); // Retire l'entête : email,date
}

// --- 2. LECTURE MESSAGES CONTACT ---
$messagesFile = 'messages_contact.csv';
$messages = [];

if (file_exists($messagesFile)) {
    $messages = array_map('str_getcsv', file($messagesFile));
    array_shift($messages); // Retire l'entête
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard DATECH INNOVA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f4f4;
        }

        h1 {
            color: #1a237e;
            text-align: center;
        }

        h2 {
            margin-top: 40px;
            color: #3949ab;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #e8eaf6;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .empty {
            font-style: italic;
            color: #777;
        }

        .container {
            width: 90%;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>Dashboard DATECH INNOVA</h1>

    <!-- --------------------- -->
    <!-- SECTION ABONNÉS -->
    <!-- --------------------- -->
    <h2>Abonnés Newsletter</h2>

    <?php if (count($subscribers) > 0) : ?>
        <table>
            <tr>
                <th>Email</th>
                <th>Date d'inscription</th>
            </tr>

            <?php foreach ($subscribers as $sub) : ?>
                <tr>
                    <td><?= htmlspecialchars($sub[0]) ?></td>
                    <td><?= htmlspecialchars($sub[1]) ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
    <?php else: ?>
        <p class="empty">Aucun abonné pour le moment.</p>
    <?php endif; ?>


    <!-- --------------------- -->
    <!-- SECTION MESSAGES CONTACT -->
    <!-- --------------------- -->
    <h2>Messages Contact</h2>

    <?php if (count($messages) > 0) : ?>
        <table>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Message</th>
                <th>Date</th>
            </tr>

            <?php foreach ($messages as $msg) : ?>
                <tr>
                    <td><?= htmlspecialchars($msg[0]) ?></td>
                    <td><?= htmlspecialchars($msg[1]) ?></td>
                    <td><?= htmlspecialchars($msg[2]) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg[3])) ?></td>
                    <td><?= htmlspecialchars($msg[4]) ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php else: ?>
        <p class="empty">Aucun message reçu pour le moment.</p>
    <?php endif; ?>

</div>

</body>
</html>
