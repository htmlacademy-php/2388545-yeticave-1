<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?= htmlspecialchars($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <?php $time_left = calculate_time_difference($lot['date']) ?>
    <?php $formatted_time_left = format_time_difference($time_left) ?>
    <?php $timer_class = (int)$time_left[0] < 1 ? "timer--finishing" : ""; ?>
    <h2><?= htmlspecialchars($lot['title']) ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= htmlspecialchars($lot['img']) ?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['category']) ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description']) ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($is_auth): ?>
                <div class="lot-item__state">
                    <div class="lot-item__timer timer <?= htmlspecialchars($timer_class) ?>">
                        <?php if ((int)$time_left[0] < 0): ?>
                            00 : 00
                        <?php else: ?>
                            <?= htmlspecialchars($formatted_time_left[0]) . " : " . htmlspecialchars($formatted_time_left[1]) ?>
                        <?php endif; ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= htmlspecialchars($current_price) ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= htmlspecialchars($lot['price']) ?></span>
                        </div>
                    </div>
                    <form class="lot-item__form" action="lot.php?id=<?= htmlspecialchars($lot['id']) ?>" method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item <?= !empty($errors['cost']) ? 'form__item--invalid' : '' ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost" placeholder="<?= htmlspecialchars($min_rate) ?>">
                            <span class="form__error"><?= htmlspecialchars($errors['cost']) ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок
                    <?php if (count($rate_history)): ?>
                        (<span><?= count($rate_history) ?></span>)
                    <?php endif; ?>
                </h3>
                <table class="history__list">
                    <?php if (!count($rate_history)): ?>
                        <p>Ставок пока нет</p>
                    <?php else: ?>
                        <?php foreach ($rate_history as $rate): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= htmlspecialchars($rate['login']) ?></td>
                                <td class="history__price"><?= htmlspecialchars($rate['cost']) ?></td>
                                <td class="history__time"><?= calculate_past_date($rate['date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</section>
