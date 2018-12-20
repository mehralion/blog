<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 29.11.2014
 * Time: 19:03
 *
 * @var array $users
 */ ?>
<strong>
    В 00:00 <?= date('d.m.Y', time()); ?> в ежедневной беспроигрышной "Зимней Волшебной лотерее" были разыграны призы:
</strong>
<br><br>
<table style="width: 100%;" class="loto">
    <colgroup>
        <col width="auto">
        <col width="35px">
        <col width="auto">
    </colgroup>
    <?php foreach ($users as $user): ?>
        <?php /** @var User $User */
        $User = $user['User']; ?>
        <tr>
            <td><?= $User->getFullLogin(); ?></td>
            <td><?= isset($user['loto']['img']) ? '<img src="'.$user['loto']['img'].'" style="max-height:30px">' : ''; ?></td>
            <td>Выиграл "<?= $user['loto']['item']; ?>"!</td>
        </tr>
    <?php endforeach; ?>
</table>
<br>
<strong>
    Все обладатели "Волшебных Снежинок" получили на счет 0.1 еврокредита за каждую "Волшебную Снежинку".
</strong>
<br><br>
Поздравляю призеров и желаю Удачи в следующую полночь!