<nav class="nav">
    <ul class="nav__list container">
    <?php foreach ($categories as $categories_list) : ?>
      <li class="nav__item">
        <a href="all-lots.php?category_id=<?= htmlspecialchars($categories_list['id']); ?>">
        <?= htmlspecialchars($categories_list['title']); ?></a>
      </li>
    <?php endforeach; ?>
    </ul>
</nav>

    <div class="container">
        <section class="lots">
            <h2>Все лоты в категории <span>«<?= htmlspecialchars($current_category);?>»</span></h2>
            <ul class="lots__list">
            <?php foreach ($lots as $lot_val) : ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($lot_val["path"]); ?>" width="350" height="260"
                        alt="<?= htmlspecialchars($lot_val["title"]); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($lot_val["category_name"]); ?></span>
                    <h3 class="lot__title">
                        <a class="text-link" href="lot.php?id=<?= htmlspecialchars($lot_val["id"]); ?>">
                    <?= htmlspecialchars($lot_val["title"]); ?></a></h3>
                    <div div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=format_price(htmlspecialchars($lot_val['price']));?></span>
                        </div>
                        <?php $warningDate = get_time_left(htmlspecialchars($lot_val['expiration']));?>
                        <div class="lot__timer timer <?=($warningDate['hours'] < 1) ? 'timer--finishing' : '';?>">
                        <?=decorate_time(htmlspecialchars($lot_val['expiration']));?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
            </ul>
        </section>
        <?php if ($pages_count > 1) : ?>
            <ul class="pagination-list">
            <?php $prev = htmlspecialchars($current_page) - 1; ?>
            <?php $next = htmlspecialchars($current_page) + 1; ?>
                <li class="pagination-item pagination-item-prev">
                    <a <?php if ($current_page >= 2) :
                        ?> href="all-lots.php?category_id=<?= htmlspecialchars($category_id);?>&page=<?= $prev; ?>"
                       <?php endif; ?>>Назад</a>
                </li>
                <?php foreach ($pages as $page) : ?>
                <li class="pagination-item <?php if ($page === $current_page) :
                    ?>pagination-item-active
                                           <?php endif;?>">
                <a href="all-lots.php?category_id=<?=htmlspecialchars($category_id);?>
                &page=<?= htmlspecialchars($page); ?>">
                    <?= htmlspecialchars($page); ?>
                </a>
                </li>
                <?php endforeach; ?>
                <li class="pagination-item pagination-item-next">
                    <a <?php if ($current_page < $pages_count) :
                        ?> href="all-lots.php?category_id=<?= htmlspecialchars($category_id);
?>&page=<?=htmlspecialchars($next); ?>"<?php
                       endif; ?>>Вперед</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
