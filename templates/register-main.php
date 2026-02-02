<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.php?category=<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form container <?= !empty($errors) ? 'form--invalid' : '' ?>" action="register.php" method="post" autocomplete="off">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?= !empty($errors['email']) ? 'form__item--invalid' : '' ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= htmlspecialchars($form_fields['email'] ?? '') ?>">
        <span class="form__error"><?= htmlspecialchars($errors['email']) ?></span>
    </div>
    <div class="form__item <?= !empty($errors['password']) ? 'form__item--invalid' : '' ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?= htmlspecialchars($errors['password']) ?></span>
    </div>
    <div class="form__item <?= !empty($errors['name']) ? 'form__item--invalid' : '' ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= htmlspecialchars($form_fields['name'] ?? '') ?>">
        <span class="form__error"><?= htmlspecialchars($errors['name']) ?></span>
    </div>
    <div class="form__item <?= !empty($errors['message']) ? 'form__item--invalid' : '' ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= htmlspecialchars($form_fields['message'] ?? '') ?></textarea>
        <span class="form__error"><?= htmlspecialchars($errors['message']) ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
