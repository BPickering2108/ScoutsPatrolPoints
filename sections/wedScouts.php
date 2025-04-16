<?php
$day = 'wedScouts';
$_GET['day'] = $day;
include '../backend/basePoints.php';

// Map day names to readable titles
$pageTitles = [
    'monScouts' => 'Monday Scouts',
    'tueExplorers' => 'Tuesday Explorers',
    'wedScouts' => 'Wednesday Scouts',
    'thurExplorers' => 'Thursday Explorers'
];

$title = $pageTitles[$day] ?? 'Patrol Points'; // Fallback title
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body data-csrf="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    <script src="/scripts/patrolPoints.js"></script>
    <?php include '../header.php'; ?>
    <h1><?= $title ?></h1>
    <div class="progress-container">
        <?php foreach (['Kestrel', 'Curlew', 'Eagle', 'Woodpecker'] as $patrol): ?>
            <div class="progress-wrapper" onclick="showButtons('<?= $patrol ?>')">
                <div class="progress-bar">
                    <div class="progress-fill" id="<?= $patrol ?>-fill"
                        style="height: <?= $data[$patrol.'_points'] ?>px; background-color: <?= $data[$patrol.'_colour'] ?>; color: <?= getTextColor($data[$patrol.'_colour']) ?>;">
                        <?= intval($data[$patrol.'_points']) ?>
                    </div>
                </div>
                <div class="progress-label"><?= $patrol ?></div>
                <div class="buttons" id="<?= $patrol ?>-buttons">
                    <button onclick="updatePoints('<?= $patrol ?>', 'increment', 1, '<?= $day ?>')">+</button>
                    <button onclick="updatePoints('<?= $patrol ?>', 'decrement', 1, '<?= $day ?>')">-</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>