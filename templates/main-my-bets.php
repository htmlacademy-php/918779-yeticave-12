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

  <section class="rates container">
      <h2>Мои ставки</h2>
      <?php if (!empty($bets)) : ?>
      <table class="rates__list">
            <?php foreach ($bets as $bet) : ?>
                <?php $winner_verify = $bet["winner_id"] === $_SESSION["id"];?>
                <?php $time = get_time_left(htmlspecialchars($bet["expiration"]));?>
                <?php if ($winner_verify) : ?>
        <tr class="rates__item rates__item--win">
                <?php else : ?>
        <tr class="rates__item <?= (array_sum($time) <= 0) ? 'rates__item--end' : '' ;?>">
                <?php endif; ?>
         <td class="rates__info">
            <div class="rates__img">
                <img src="<?= htmlspecialchars($bet["path"]); ?>" width="54" height="40"
                    alt="<?= htmlspecialchars($bet["title"]); ?>">
            </div>
            <div>
                <h3 class="rates__title"><a href="lot.php?id=<?= htmlspecialchars($bet["id"]); ?>">
                    <?= htmlspecialchars($bet["title"]); ?></a></h3>
                <p><?php if ($winner_verify) :
                    ?><?= htmlspecialchars($bet["message"]); ?><?php
                   endif; ?></p>
            </div>
          </td>
          <td class="rates__category">
                <?= htmlspecialchars($bet["category"]); ?>
          </td>
          <td class="rates__timer">
                <?php if ($time['hours'] > 0 || $time['hours'] === 0 && $time['minutes'] > 0) : ?>
          <div class="timer <?=($time['hours'] < 1) ? 'timer--finishing' : '';?>">
                    <?= sprintf("%02d:%02d", htmlspecialchars($time['hours']), htmlspecialchars($time['minutes'])); ?>
          </div>
                <?php else : ?>
                    <?php if ($winner_verify) : ?>
                <div class="timer timer--win">Ставка выиграла</div>
                    <?php else : ?>
                <div class="timer timer--end">Торги окончены</div>
                    <?php endif; ?>
                <?php endif; ?>
          </td>
          <td class="rates__price">
                <?=format_price(htmlspecialchars($bet["cost"]));?>
          </td>
                <?php $bet_time = get_time_after_end(htmlspecialchars($bet["date_bet"])); ?>
          <td class="rates__time">
                <?= htmlspecialchars($bet_time); ?>
          </td>
        </tr>
            <?php endforeach; ?>
      </table>
      <?php endif; ?>
    </section>
