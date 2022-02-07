  <nav class="nav">
    <ul class="nav__list container">
    <?php foreach ($categories as $category_val): ?>
      <li class="nav__item">
        <a href="all-lots.php?category_id=<?= $category_val['id']; ?>"><?= $category_val['title']; ?></a>
      </li>
    <?php endforeach; ?>
    </ul>
  </nav>

  <section class="rates container">
      <h2>Мои ставки</h2>
      <?php if (!empty($bets)): ?>
      <table class="rates__list">
      <?php foreach($bets as $bet): ?>
        <tr class="rates__item <?php if ($bet["winner_id"] === $_SESSION["id"]): ?>rates__item--win<?php endif; ?>">
          <td class="rates__info">
            <div class="rates__img">
            </div>
            <h3 class="rates__title"><a href="lot.php?id=<?= $bet["id"]; ?>"><?= $bet["title"]; ?></a></h3>
            <p><?php if ($bet["winner_id"] === $_SESSION["id"]): ?><?= $bet["user_data"] ?><?php endif; ?></p>
          </td>
          <td class="rates__category">
          <?= $bet["title"]; ?>
          </td>
          <td class="rates__timer">
          <?php if ($bet["winner_id"] === $_SESSION["id"]): ?>
            <div class="timer timer--win">Ставка выиграла</div>
          <?php else: ?>
          <?php $time = get_time_left($bet["expiration"]) ?>
            <div class="timer <?php if ($time["hours"] < 1 && $time["hours"] != 0): ?>timer--finishing <?php elseif($time["hours"] == 0): ?>timer--end<?php endif; ?>">
              <?php if ($time["hours"] != 0): ?>
              <?= "$time[hours] : $time[minutes]"; ?>
              <?php else: ?>
                Торги окончены
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </td>
          <td class="rates__price">
          <?=format_price($bet["cost"]);?>
          </td>
          <td class="rates__time">
          <?= $bet["date_bet"]; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php endif; ?>
    </section>