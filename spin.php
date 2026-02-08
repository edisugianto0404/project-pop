<?php
header('Content-Type: application/json');

$spinsFile = __DIR__ . '/data/spins.json';
if (!file_exists($spinsFile)) {
    file_put_contents($spinsFile, json_encode([]));
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$fp = fopen($spinsFile, 'c+');
if ($fp === false) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak dapat membuka data spin']);
    exit;
}

flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$spinsData = json_decode($contents ?: '[]', true);
if (!is_array($spinsData)) {
    $spinsData = [];
}

if (in_array($clientIp, $spinsData, true)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(['status' => 'blocked', 'message' => 'IP sudah spin']);
    exit;
}

$spinsData[] = $clientIp;
rewind($fp);
ftruncate($fp, 0);
fwrite($fp, json_encode($spinsData, JSON_PRETTY_PRINT));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['status' => 'ok', 'result' => 'COBA LAGI']);
