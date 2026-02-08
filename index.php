<?php
$spinsFile = __DIR__ . '/data/spins.json';
if (!file_exists($spinsFile)) {
    file_put_contents($spinsFile, json_encode([]));
}
$spinsData = json_decode(file_get_contents($spinsFile), true);
if (!is_array($spinsData)) {
    $spinsData = [];
}
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$hasSpun = in_array($clientIp, $spinsData, true);

$winnersFile = __DIR__ . '/data/winners.json';
$winnersData = [];
if (file_exists($winnersFile)) {
    $decoded = json_decode(file_get_contents($winnersFile), true);
    if (is_array($decoded)) {
        $winnersData = $decoded;
    }
}
$latestWinners = array_slice($winnersData, -10);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin Wheel Bonus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <button id="openPopup" class="primary-button">🎁 CLAIM BONUS – KLIK DI SINI</button>
    </div>

    <div id="bonusPopup" class="popup-overlay" aria-hidden="true">
        <div class="popup-card" role="dialog" aria-modal="true">
            <button id="closePopup" class="close-button" aria-label="Tutup">✖</button>
            <div class="popup-grid">
                <section class="wheel-section">
                    <h2>Spin Wheel Bonus</h2>
                    <div class="wheel-wrapper">
                        <canvas id="spinWheel" width="320" height="320"></canvas>
                        <div class="pointer"></div>
                    </div>
                    <button id="spinButton" class="spin-button" data-used="<?php echo $hasSpun ? '1' : '0'; ?>" <?php echo $hasSpun ? 'disabled' : ''; ?>>
                        <?php echo $hasSpun ? 'SPIN SUDAH DIGUNAKAN' : 'SPIN SEKARANG'; ?>
                    </button>
                    <div id="spinResult" class="spin-result">
                        <?php if ($hasSpun): ?>
                            SPIN SUDAH DIGUNAKAN
                        <?php else: ?>
                            Siap untuk spin pertama Anda!
                        <?php endif; ?>
                    </div>
                    <div class="winners">
                        <h3>Pemenang Terbaru</h3>
                        <ul>
                            <?php foreach (array_reverse($latestWinners) as $winner): ?>
                                <li><?php echo htmlspecialchars($winner, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
                <aside class="affiliate-section">
                    <h3>Affiliate Resmi</h3>
                    <ul class="affiliate-list">
                        <li><a href="#">Bonus Slots Maxwin</a></li>
                        <li><a href="#">Promo Deposit 100%</a></li>
                        <li><a href="#">Cashback Harian</a></li>
                        <li><a href="#">VIP Member Rewards</a></li>
                        <li><a href="#">Lucky Spin Mingguan</a></li>
                        <li><a href="#">Turnamen Slot Premium</a></li>
                        <li><a href="#">Referral Komisi 30%</a></li>
                        <li><a href="#">Bonus Rollingan</a></li>
                    </ul>
                </aside>
            </div>
        </div>
    </div>

    <script src="spin.js"></script>
</body>
</html>
