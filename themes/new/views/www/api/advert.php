<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.11.2014
 * Time: 16:26
 *
 * @var string $link
 * @var string $hash
 * @var string $hash2
 */ ?>

<form method="post" id="auth" action="<?= $link ?>">
    <input type="hidden" name="hash" value="<?= $hash ?>">
    <input type="hidden" name="hash2" value="<?= $hash2 ?>">
</form>
<script>document.getElementById("auth").submit();</script>