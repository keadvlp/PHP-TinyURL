<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Kısaltıcı</title>
</head>
<body>
    <h1>Link Kısaltıcı</h1>
    <form method="post" action="">
        <label for="longUrl">Kısaltılacak URL:</label>
        <input type="url" id="longUrl" name="longUrl" required>
        <button type="submit">Kısalt</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $longUrl = $_POST['longUrl'];
        $shortUrl = shortenUrl($longUrl);
        if (filter_var($shortUrl, FILTER_VALIDATE_URL)) {
            echo "<p>Kısaltılmış URL: <a href=\"$shortUrl\" target=\"_blank\">$shortUrl</a></p>";
        } else {
            echo "<p>Linki yukarıda yer alan boşluğa girin.</p>";
        }
    }

    function shortenUrl($longUrl) {
        $apiUrl = "https://api.tinyurl.com/create";
        $apiKey = "iQM3TGNdNoW0Y5Qo3WIRA0PbEyRjb8vjX3ghO1s7hq3pkwNMK7diW7p0dqIR"; // API anahtarınız

        // TinyURL API'sine gönderilecek veri
        $data = array(
            'url' => $longUrl,
            'domain' => 'tiny.one'
        );

        // cURL oturumu başlatma
        $ch = curl_init($apiUrl);

        // cURL seçeneklerini ayarlama
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ));

        // API'den yanıt alma
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Yanıtı işleme
        if ($httpCode == 200 || $httpCode == 201) {  // Başarılı yanıt kodları
            $responseData = json_decode($response, true);
            if (isset($responseData['data']['tiny_url'])) {
                return $responseData['data']['tiny_url'];
            } else {
                return "Hata: " . (isset($responseData['errors'][0]) ? $responseData['errors'][0] : "Beklenmeyen dönüş formatı.");
            }
        } else {
            return "Hata: Link kısaltmada hata oluştu. HTTP Kodu: " . $httpCode . " Dönüş: " . $response;
        }
    }
    ?>
</body>
</html>
