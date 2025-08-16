<?php
// 1. HTTP-Header setzen, um dem Browser mitzuteilen, dass wir Text zurücksenden.
header('Content-Type: text/plain; charset=utf-8');

// 2. Prüfen, ob die Anfrage mit der POST-Methode gesendet wurde.
// Dein JavaScript sendet eine POST-Anfrage, also sollten wir andere Methoden ablehnen.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo "Fehler: Nur POST-Anfragen sind erlaubt.";
    exit; // Skript beenden
}

// 3. Den rohen POST-Body auslesen.
// Da dein JavaScript die Daten als JSON-String im Body sendet (Content-Type: application/json),
// können wir nicht die Standard-PHP-Variable `$_POST` verwenden.
$json_data = file_get_contents('php://input');

// 4. Den JSON-String in ein PHP-Array umwandeln.
$content_array = json_decode($json_data, true); // `true` wandelt es in ein assoziatives Array um.

// 5. Überprüfen, ob die JSON-Daten gültig waren.
if ($content_array === null) {
    http_response_code(400); // 400 Bad Request
    echo "Fehler: Ungültige JSON-Daten empfangen.";
    exit;
}

// 6. Das PHP-Array wieder in einen schön formatierten JSON-String umwandeln.
// JSON_PRETTY_PRINT sorgt dafür, dass die resultierende content.json lesbar ist.
// JSON_UNESCAPED_UNICODE stellt sicher, dass Umlaute (ä, ö, ü) korrekt gespeichert werden.
$json_to_save = json_encode($content_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// 7. Den JSON-String in die Datei 'content.json' schreiben.
// `file_put_contents` überschreibt die Datei jedes Mal komplett.
$file_path = 'content.json';
if (file_put_contents($file_path, $json_to_save) !== false) {
    // Wenn das Schreiben erfolgreich war, sende eine Erfolgsmeldung zurück.
    http_response_code(200); // 200 OK
    echo "Inhalte erfolgreich gespeichert!";
} else {
    // Wenn das Schreiben fehlgeschlagen ist, sende eine Fehlermeldung.
    http_response_code(500); // 500 Internal Server Error
    echo "Fehler: Inhalte konnten nicht gespeichert werden. Überprüfen Sie die Serverberechtigungen.";
}
?>