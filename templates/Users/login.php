<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users form content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Log in') ?></legend>
        <?= $this->Form->control('email') ?>
        <?= $this->Form->control('password') ?>
    </fieldset>
    <?= $this->Form->button(__('Log in')) ?>
    <?= $this->Form->end() ?>
    <p><?= __("Don't have an account?") ?> <?= $this->Html->link(__('Register'), ['action' => 'add']) ?></p>
</div>
