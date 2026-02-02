<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.php?category=<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form container <?= !empty($errors) ? 'form--invalid' : '' ?>" action="login.php<?= htmlspecialchars($intended_param) ?>" method="post">
    <h2>Вход</h2>
    <div class="form__item <?= !empty($errors['email']) ? 'form__item--invalid' : '' ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail">
        <span class="form__error"><?= htmlspecialchars($errors['email']) ?></span>
    </div>
    <div class="form__item form__item--last <?= !empty($errors['password']) ? 'form__item--invalid' : '' ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?= htmlspecialchars($errors['password']) ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
