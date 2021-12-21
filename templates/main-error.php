        <section class="lot-item container">
            <?php if (http_response_code(403)): ?>
            <h2>403 Доступ запрещен</h2>
            <p>У вас нет прав доступа к данному разделу сайта.</p>

            <?php else: ?>
             <h2>404 Страница не найдена</h2>
            <p>Данной страницы не существует на сайте.</p>

            <?php endif; ?>

        </section>
