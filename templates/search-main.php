<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?= htmlspecialchars($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($search_string) ?></span>»</h2>
        <?php if (!count($lots)): ?>
            <span>Ничего не найдено по вашему запросу</span>
        <?php else: ?>
            <ul class="lots__list">
                <?php foreach ($lots as $lot): ?>
                    <?php $time_left = calculate_time_difference($lot['date']) ?>
                    <?php $formatted_time_left = format_time_difference($time_left) ?>
                    <?php $timer_class = (int)$time_left[0] < 1 ? "timer--finishing" : ""; ?>
                    <?php if ((int)$time_left[0] >= 0): ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= $lot['img'] ?? 'img/placeholder.jpg' ?>" width="350" height="260" alt="">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= htmlspecialchars($lot['category'] ?? 'Прочее') ?></span>
                                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= htmlspecialchars($lot['id']) ?>"><?= htmlspecialchars($lot['title'] ?? 'Без названия') ?></a></h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= format_price($lot['price'] ?? 0) ?></span>
                                    </div>
                                    <div class="lot__timer timer <?= htmlspecialchars($timer_class) ?>">
                                        <?php if ((int)$time_left[0] < 0): ?>
                                            00 : 00
                                        <?php else: ?>
                                            <?= htmlspecialchars($formatted_time_left[0]) . " : " . htmlspecialchars($formatted_time_left[1]) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
    </ul>
</div>
