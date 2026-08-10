<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сравнение текстов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .input-area {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .input-group {
            flex: 1;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .input-group textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
            box-sizing: border-box;
        }
        .input-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .button-area {
            text-align: center;
            margin: 20px 0;
        }
        .compare-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 40px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .compare-btn:hover {
            background: #45a049;
        }
        .result-area {
            margin-top: 30px;
            padding: 20px;
            background: #fafafa;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: none;
        }
        .result-area h2 {
            margin-top: 0;
            color: #333;
        }
        .result-text {
            line-height: 1.8;
            font-size: 16px;
        }
        .result-text .sentence {
            display: inline-block;
            margin: 2px 0;
            padding: 2px 4px;
            border-radius: 3px;
            transition: all 0.2s;
        }
        .result-text .same {
            background: transparent;
        }
        .result-text .new {
            background: #a8e6cf;
        }
        .result-text .changed {
            background: #ffd93d;
            cursor: pointer;
            position: relative;
        }
        .result-text .changed:hover {
            background: #ffd93d;
        }
        .result-text .changed .old-version {
            display: none;
            background: #ffd93d;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .result-text .changed:hover .old-version {
            display: inline;
        }
        .result-text .changed:hover .new-version {
            display: none;
        }
        .result-text .deleted {
            background: #ff8a8a;
            text-decoration: line-through;
        }
        .legend {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .legend-item.new {
            background: #a8e6cf;
        }
        .legend-item.changed {
            background: #ffd93d;
        }
        .legend-item.deleted {
            background: #ff8a8a;
        }
        .stats {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .input-area {
                flex-direction: column;
            }
            .input-group textarea {
                height: 150px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Сравнение текстов</h1>

    <form method="POST">
        <div class="input-area">
            <div class="input-group">
                <label for="text1">Первая версия текста:</label>
                <textarea id="text1" name="text1" placeholder="Введите первый текст..."><?= htmlspecialchars($_POST['text1'] ?? '') ?></textarea>
            </div>
            <div class="input-group">
                <label for="text2">Вторая версия текста:</label>
                <textarea id="text2" name="text2" placeholder="Введите второй текст..."><?= htmlspecialchars($_POST['text2'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="button-area">
            <button type="submit" class="compare-btn">Сравнить</button>
        </div>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['text1']) && !empty($_POST['text2'])):
        $result = compareTexts($_POST['text1'], $_POST['text2']);

        $stats = [
            'same' => 0,
            'new' => 0,
            'changed' => 0,
            'deleted' => 0
        ];
        foreach ($result as $item) {
            $stats[$item['type']]++;
        }
        ?>
        <div class="result-area" style="display: block;">
            <h2>Результат сравнения:</h2>

            <div class="result-text">
                <?php foreach ($result as $item): ?>
                    <span class="sentence <?= $item['type'] ?>">
                        <?php if ($item['type'] === 'changed'): ?>
                            <span class="new-version"><?= htmlspecialchars($item['text']) ?></span>
                            <span class="old-version"><?= htmlspecialchars($item['old']) ?></span>
                        <?php else: ?>
                            <?= htmlspecialchars($item['text']) ?>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <div class="stats">
                <strong>Статистика:</strong>
                Совпадений: <?= $stats['same'] ?>,
                Добавлено: <?= $stats['new'] ?>,
                Изменено: <?= $stats['changed'] ?>,
                Удалено: <?= $stats['deleted'] ?>
            </div>

            <div class="legend">
                <span class="legend-item new">🟢 Новые предложения</span>
                <span class="legend-item changed">🟡 Измененные предложения (наведите для просмотра старой версии)</span>
                <span class="legend-item deleted">🔴 Удаленные предложения</span>
            </div>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div style="color: red; padding: 10px; background: #ffebee; border-radius: 5px; margin-top: 20px;">
            Пожалуйста, заполните оба текстовых поля для сравнения.
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    });
</script>

</body>
</html>

<?php

// разбитие текста
function splitSentences($text) {
    // учитываем точки, вопросительные и восклицательные знаки как разделитеи
    $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    // Если текст пустой или не содержит знаков препинания
    if (empty($sentences)) {
        return array_filter(array_map('trim', explode("\n", $text)), 'strlen');
    }
    return array_map('trim', $sentences);
}

// сравнение текстов
function compareTexts($oldText, $newText) {
    $oldSentences = splitSentences($oldText);
    $newSentences = splitSentences($newText);

    $result = [];
    $oldIndex = 0;
    $newIndex = 0;

    // Сначала ищем совпадения и изменения
    while ($oldIndex < count($oldSentences) && $newIndex < count($newSentences)) {
        $oldSent = $oldSentences[$oldIndex];
        $newSent = $newSentences[$newIndex];

        // Проверяем точное совпадение
        if ($oldSent === $newSent) {
            $result[] = ['type' => 'same', 'text' => $newSent, 'old' => $oldSent];
            $oldIndex++;
            $newIndex++;
            continue;
        }

        // Проверяем похожие предложения
        $similarity = 0;
        similar_text($oldSent, $newSent, $similarity);

        if ($similarity > 70) {
            $result[] = ['type' => 'changed', 'text' => $newSent, 'old' => $oldSent];
            $oldIndex++;
            $newIndex++;
            continue;
        }

        // Ищем текущее новое предложение в старом тексте (это может быть вставка)
        $foundInOld = false;
        for ($i = $oldIndex + 1; $i < count($oldSentences); $i++) {
            if ($oldSentences[$i] === $newSent) {
                // Пропускаем удаленные старые предложения
                for ($j = $oldIndex; $j < $i; $j++) {
                    $result[] = ['type' => 'deleted', 'text' => $oldSentences[$j], 'old' => $oldSentences[$j]];
                }
                $oldIndex = $i + 1;
                $result[] = ['type' => 'same', 'text' => $newSent, 'old' => $newSent];
                $newIndex++;
                $foundInOld = true;
                break;
            }
        }

        if ($foundInOld) continue;

        // Ищем текущее старое предложение в новом тексте (это может быть удаление)
        $foundInNew = false;
        for ($i = $newIndex + 1; $i < count($newSentences); $i++) {
            if ($newSentences[$i] === $oldSent) {
                // Пропускаем новые предложения (вставки)
                for ($j = $newIndex; $j < $i; $j++) {
                    $result[] = ['type' => 'new', 'text' => $newSentences[$j], 'old' => ''];
                }
                $newIndex = $i + 1;
                $result[] = ['type' => 'same', 'text' => $oldSent, 'old' => $oldSent];
                $oldIndex++;
                $foundInNew = true;
                break;
            }
        }

        if ($foundInNew) continue;

        // Если ничего не подошло - считаем изменением
        $result[] = ['type' => 'changed', 'text' => $newSent, 'old' => $oldSent];
        $oldIndex++;
        $newIndex++;
    }

    // Обрабатываем оставшиеся предложения
    while ($oldIndex < count($oldSentences)) {
        $result[] = ['type' => 'deleted', 'text' => $oldSentences[$oldIndex], 'old' => $oldSentences[$oldIndex]];
        $oldIndex++;
    }

    while ($newIndex < count($newSentences)) {
        $result[] = ['type' => 'new', 'text' => $newSentences[$newIndex], 'old' => ''];
        $newIndex++;
    }

    return $result;
}
?>



