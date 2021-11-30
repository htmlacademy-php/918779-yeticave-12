    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $categories_list):?>
            <li class="promo__item promo__item--<?= $categories_list['code']; ?>">
                <a class="promo__link" href="all-lots.php?category_id=<?= $categories_list['id']; ?>"><?=htmlspecialchars($categories_list['title']);?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?php foreach ($lots as $lots_key => $lots_val):?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($lots_val['path']);?>" width="350" height="260" alt="<?=htmlspecialchars($lots_val['title']);?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($lots_val['category']);?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $lots_val['id']; ?>"><?=htmlspecialchars($lots_val['title']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=format_price($lots_val['price']);?></span>
                        </div>
                        <?php $warningDate = get_time_left($lots_val['expiration']);?>
                        <div class="lot__timer timer <?=($warningDate['hours'] < 1) ? 'timer--finishing' : '';?>">
                            <?=decorate_time($lots_val['expiration']);?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
