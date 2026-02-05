<div class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php foreach ($categories as $category) : ?>
                <li class="promo__item promo__item--<?= htmlspecialchars($category['slug'] ?? '') ?>">
                    <a class="promo__link" href="all-lots.php?category=<?= htmlspecialchars($category['id'] ?? '') ?>"><?= htmlspecialchars($category['name'] ?? '') ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($lots as $lot) : ?>
                <?php $time_left = isset($lot['date']) ? calculate_time_difference($lot['date']) : 0 ?>
                <?php $formatted_time_left = (isset($time_left) && is_array($time_left)) ? format_time_difference($time_left) : ['00', '00'] ?>
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
                                    <?= htmlspecialchars($formatted_time_left[0] ?? 0) . " : " . htmlspecialchars($formatted_time_left[1] ?? 0) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
