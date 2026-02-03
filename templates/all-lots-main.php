<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
            <li class="nav__item">
                <a href="all-lots.php?category=<?= htmlspecialchars($category['id'] ?? '') ?>"><?= htmlspecialchars($category['name'] ?? '') ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span>«<?= htmlspecialchars($category_title) ?>»</span></h2>
        <?php if ($lots_count > 0) : ?>
            <ul class="lots__list">
                <?php foreach ($lots as $lot) : ?>
                    <?php $time_left = calculate_time_difference($lot['date'] ?? '') ?>
                    <?php $formatted_time_left = format_time_difference($time_left) ?>
                    <?php $timer_class = (int)$time_left[0] < 1 ? "timer--finishing" : ""; ?>
                    <?php if ((int)$time_left[0] >= 0) : ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= $lot['img'] ?? 'img/placeholder.jpg' ?>" width="350" height="260" alt="">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= htmlspecialchars($lot['category'] ?? 'Прочее') ?></span>
                                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= htmlspecialchars($lot['id'] ?? '') ?>"><?= htmlspecialchars($lot['title'] ?? 'Без названия') ?></a></h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= format_price($lot['price'] ?? 0) ?></span>
                                    </div>
                                    <div class="lot__timer timer <?= htmlspecialchars($timer_class) ?>">
                                        <?php if ((int)$time_left[0] < 0) : ?>
                                            00 : 00
                                        <?php else : ?>
                                            <?= htmlspecialchars($formatted_time_left[0]) . " : " . htmlspecialchars($formatted_time_left[1]) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>Ничего не найдено</p>
        <?php endif; ?>
    </section>

    <?php if ($total_pages > 1) : ?>
        <ul class="pagination-list">
            <?php if ($current_page > 1) : ?>
                <li class="pagination-item pagination-item-prev">
                    <a href="/all-lots.php?category=<?= htmlspecialchars($category_id) ?>&page=<?= htmlspecialchars($current_page) - 1 ?>">Назад</a>
                </li>
            <?php else : ?>
                <li class="pagination-item pagination-item-prev pagination-item-disabled">
                    <a>Назад</a>
                </li>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                <li class="pagination-item <?= ($page === $current_page) ? 'pagination-item-active' : '' ?>">
                    <a href="/all-lots.php?category=<?= htmlspecialchars($category_id) ?>&page=<?= htmlspecialchars($page) ?>"><?= htmlspecialchars($page) ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages) : ?>
                <li class="pagination-item pagination-item-next">
                    <a href="/all-lots.php?category=<?= htmlspecialchars($category_id) ?>&page=<?= htmlspecialchars($current_page) + 1 ?>">Вперед</a>
                </li>
            <?php else : ?>
                <li class="pagination-item pagination-item-next pagination-item-disabled">
                    <a>Вперед</a>
                </li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>
