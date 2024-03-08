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
    <form class='boxForm' action="index.php" method="post">
        <label for="theme">Theme :
            <input type="theme" id="theme" name="theme" required>
        </label>
        <label for="hero">Quel est le prénom du hero de l'histoire ?
            <input type="text" id="hero" name="hero" required>
        </label>


        <label for="age">Quel âge as-tu ?
            <input type="number" id="age" name="age" required>
        </label>

        <input class='btnValider' type="submit" value="Valider mes choix">
    </form>


    <p class='prompt'>Il était une fois, dans un royaume lointain, un jeune chevalier nommé Arthur, qui rêvait de devenir le plus grand protecteur du royaume. Arthur vivait dans un petit village paisible entouré de champs verdoyants et de montagnes majestueuses. Un jour, le roi du royaume fit appel à tous les jeunes chevaliers en herbe pour affronter un dragon féroce qui menaçait le village voisin. Arthur décida de relever le défi et se lança dans une aventure périlleuse pour sauver le village. Armé de son épée brillante et de son courage sans faille, Arthur se mit en route vers la grotte du dragon. En chemin, il rencontra une gentille fée qui lui offrit une potion magique pour l'aider dans sa quête. Grâce à la potion, Arthur devint plus fort et plus agile, prêt à affronter le dragon. Arrivé devant la grotte, Arthur fit face au dragon, qui crachait du feu et rugissait de colère. Mais le jeune chevalier ne se laissa pas impressionner. Il bondit en avant et attaqua le dragon avec courage et détermination. Après un combat épique, Arthur parvint à vaincre le dragon grâce à son habileté et à sa bravoure. Le village était sauvé, et le roi le remercia pour son courage et sa vaillance. Arthur était désormais un véritable chevalier, respecté de tous pour ses exploits héroïques. Il avait réalisé son rêve de devenir le protecteur du royaume, et il continua à défendre les plus faibles et à combattre le mal partout où il se trouvait. Et c'est ainsi que le jeune chevalier Arthur devint une légende, dont l'histoire était racontée aux enfants du royaume pour les inspirer et leur montrer que rien n'est impossible lorsqu'on croit en soi et qu'on fait preuve de courage.</p>
    <?php
    require __DIR__ . '/vendor/autoload.php'; //
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
    $dotenv->load(); 

    $apiKey = getenv('OPENAI_API_KEY');
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $theme = $_POST["theme"];
        $age = $_POST['age'];
        $hero = $_POST['hero'];

        // Ajouter plus de contexte dans le prompt et augmenter le nombre maximal de jetons
        $prompt = "Je veux que tu me racontes une histoire qui parle de '$theme'. Le personnage principale se nomme '$hero' Je veux que l'histoire soit pour un enfant de '$age' ans.
        L'histoire doit commencer par 'Il était une fois'.";

        // Ajout de la demande de l'utilisateur dans le message
        $promptData = array(
            "model" => "gpt-3.5-turbo",
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => $prompt
                )
            )
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