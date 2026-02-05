<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
            <li class="nav__item">
                <a href="all-lots.php?category=<?= htmlspecialchars($category['id'] ?? '') ?>"><?= htmlspecialchars($category['name'] ?? '') ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($rates as $rate) : ?>
            <?php $time_left = isset($rate['lot_end_date']) ? calculate_time_difference($rate['lot_end_date']) : 0 ?>
            <?php $formatted_time_left = (isset($time_left) && is_array($time_left)) ? format_time_difference($time_left) : ['00', '00'] ?>
            <?php $timer_class = (int)($time_left[0] ?? 0) < 1 ? "timer--finishing" : ""; ?>

            <?php if ($user_id === ($rate['winner_id'] ?? '')) : ?>
                <?php $item_class = 'rates__item--win' ?>
            <?php elseif ((int)($time_left[0] ?? 0) < 0) : ?>
                <?php $item_class = 'rates__item--end' ?>
            <?php else : ?>
                <?php $item_class = '' ?>
            <?php endif; ?>

            <tr class="rates__item <?= $item_class ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= htmlspecialchars($rate['img'] ?? '') ?>" width="54" height="40" alt="<?= htmlspecialchars($rate['lot_name'] ?? '') ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a href="lot.php?id=<?= htmlspecialchars($rate['lot_id'] ?? '') ?>"><?= htmlspecialchars($rate['lot_name'] ?? '') ?></a></h3>
                        <?php if ($user_id === ($rate['winner_id'] ?? '')) : ?>
                            <p><?= htmlspecialchars($rate['contact'] ?? '') ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= htmlspecialchars($rate['category'] ?? '') ?>
                </td>
                <td class="rates__timer">
                    <?php if ($user_id === ($rate['winner_id'] ?? '')) : ?>
                        <div class="timer timer--win">Ставка выиграла</div>
                    <?php elseif ((int)$time_left[0] < 0) : ?>
                        <div class="timer timer--end">Торги окончены</div>
                    <?php else : ?>
                        <div class="timer <?= htmlspecialchars($timer_class) ?>">
                            <?= htmlspecialchars($formatted_time_left[0] ?? 0) . " : " . htmlspecialchars($formatted_time_left[1] ?? 0) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= htmlspecialchars($rate['cost'] ?? '') ?> р
                </td>
                <td class="rates__time">
                    <?= isset($rate['rate_date']) ? calculate_past_date($rate['rate_date']) : '' ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
