<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $categories_list) :?>
        <li class="nav__item">
        <a href="all-lots.php?category_id=<?= htmlspecialchars($categories_list['id']); ?>">
            <?=htmlspecialchars($categories_list['title']); ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?= htmlspecialchars($lot['title']); ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=htmlspecialchars($lot['path']); ?>" width="730" height="548"
                alt="<?= htmlspecialchars($lot['title']); ?>">
          </div>
          <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['category']); ?></span></p>
          <p class="lot-item__description"><?=htmlspecialchars($lot['description']); ?></p>
        </div>
        <div class="lot-item__right">
          <div class="lot-item__state">
            <?php $warningDate = get_time_left(htmlspecialchars($lot['expiration']));?>
            <div class="lot-item__timer timer <?=($warningDate['hours'] < 1) ? 'timer--finishing' : '';?>">
                <?=decorate_time(htmlspecialchars($lot['expiration']));?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=format_price(htmlspecialchars($lot['price']));?></span>
              </div>
              <div class="lot-item__min-cost">
              Мин. ставка <span><?=format_price(htmlspecialchars($min_bet));?></span>
              </div>
            </div>
            <?php if (
                $is_auth
                && array_sum($warningDate) > 0
                && (int) $lot['user_id'] !== $_SESSION['id']
                && $current_user !== $_SESSION['id']
) : ?>
            <form class="lot-item__form" action="lot.php?id=<?= htmlspecialchars($id_num);?>"
                method="post" autocomplete="off">
              <p class="lot-item__form-item form__item <?php if ($error) :
                    ?>form__item--invalid
                                                       <?php endif; ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="<?=format_price(htmlspecialchars($min_bet));?>">
                <span class="form__error"><?= $error; ?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
        <?php if (!empty($history)) : ?>
          <div class="history">
            <h3>История ставок (<span><?= $bet_counter;?></span>)</h3>
            <table class="history__list">
              <?php foreach ($history as $bet) : ?>
                    <?php $bet_time = get_time_after_end(htmlspecialchars($bet["date_bet"])); ?>
              <tr class="history__item">
                <td class="history__name"><?= htmlspecialchars($bet["name"]); ?></td>
                <td class="history__price"><?=format_price(htmlspecialchars($bet['cost']));?></td>
                <td class="history__time"><?=htmlspecialchars($bet_time); ?></td>
              </tr>
              <?php endforeach; ?>
            </table>
        <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
