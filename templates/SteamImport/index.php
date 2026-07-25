<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="steam-import content">
    <h2><?= __('Import from Steam') ?></h2>
    <p class="meta">
        <?= __('Enter your Steam profile URL, vanity name, or SteamID64. Your profile and game details must be set to public in your Steam privacy settings.') ?>
    </p>

    <?= $this->Form->create() ?>
    <?= $this->Form->control('profile', [
        'label' => __('Steam profile'),
        'placeholder' => __('e.g. https://steamcommunity.com/id/yourname'),
    ]) ?>
    <?= $this->Form->button(__('Fetch library')) ?>
    <?= $this->Form->end() ?>
</div>
