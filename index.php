<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Il était une fois AI</title>
</head>

<body>
    <h1>Il était une fois ...</h1>
    <form action="index.php" method="post">
        <div class='boxForm'>
            <div class='labelBox'>
                <label for="theme">Thème : 
                    <input type="theme" id="theme" name="theme" required>
                </label>
                <label for="hero">Quel est le prénom du héros de l'histoire ?
                <input type="text" id="hero" name="hero" required>
                </label>
                <label for="age">Quel âge as-tu ? 
                <input type="number" id="age" name="age" required>
                </label>
            </div>
        </div>
        <div class='btnValider'>
            <input type="submit" value="Valider mes choix">
        </div>
    </form>
    <hr>
    <?php
    require __DIR__ . '/vendor/autoload.php';

    // Charger les variables d'environnement depuis le fichier .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $apiKey = $_ENV['OPENAI_API_KEY'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validation et nettoyage des données
        $theme = trim($_POST["theme"]);
        $hero = trim($_POST["hero"]);
        $age = intval($_POST["age"]);

        // Vérification de la validité des données
        if (empty($theme) && empty($hero) && $age != 0) {
            echo "Il faut que tu remplisses tous les éléments.";
            exit; // Arrête l'exécution du script si les données ne sont pas valides
        }

        // Nettoyage des données
        $theme = htmlspecialchars($theme);
        $hero = htmlspecialchars($hero);

        // Traitement de la requête
        $prompt = "Je veux que tu me racontes une histoire qui parle de '$theme'. Le personnage principale se nomme '$hero' Je veux que l'histoire soit pour un enfant de '$age' ans.
        L'histoire doit commencer par 'Il était une fois'. Il ne doit y avoir aucun contenu grossier, pornographique, et sexuel. L'histoire doit être bienveillante";

        // Ajout de la demande de l'utilisateur dans le message
        $promptData = array(
            "model" => "gpt-3.5-turbo",
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => $prompt
                )
            ),

        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($promptData),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $apiKey"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "Erreur lors de l'appel à l'API de ChatGPT : " . $err;
        } else {
            $decodedResponse = json_decode($response, true);
            echo "<p class='prompt'>" . $decodedResponse['choices'][0]['message']['content'] . "</p>";
        }
    }
    ?>
</body>

</html>