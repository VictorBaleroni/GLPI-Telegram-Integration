<?php

include ("../../../inc/includes.php");

Session::checkLoginUser();

use GlpiPlugin\TelegramIntegra\Telegram\TeleIntegra;

Html::header(__('Configuração do Plugin', 'telegramintegra'), $_SERVER['PHP_SELF'], "config", "plugins");

echo '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST">';
  echo Html::input('name', ['placeholder' => 'name']);
  echo Html::input('token', ['placeholder' => 'token']);
  echo Html::submit('enviar', ['class' => 'submit-token']);
Html::closeForm();

$config = new PluginTelegramintegraConfig();

if($_POST['name'] && $_POST['token']){
  $config->registerBot($_POST);
}

$botList = $config->showBots();

  foreach($botList as $value): ?>
    <label class="selectable-item <?=$value['state'] ? 'selected' : ''; ?>" for="item-<?= $value['id'] ?>">
      <input type="radio" name="botEnabled" value="<?= $value['id'] ?>" id="item-<?= $value['id'] ?>" <?= $value['state'] ? 'checked' : ''; ?> onchange="updateList(this)">
    <span><?= $value['name'] ?></span>
    </label>
  <?php endforeach; ?>

<?php
echo '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST">';
    echo Html::hidden('item_id', ['id' => 'item-id-env']);
    echo Html::submit('Selecionar', ['class' => 'select-token']);
Html::closeForm();

if($_POST['item_id']){
  $config->selectedBot($_POST);
}

Html::footer();
