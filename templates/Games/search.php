<?php
/**
 * @var \App\View\AppView $this
 * @var string $query
 * @var array<int, array<string, mixed>> $results
 * @var string|null $error
 */
?>
<div class="games search content">
    <h2><?= __('Search games') ?></h2>

    <?= $this->Form->create(null, ['type' => 'get']) ?>
    <?= $this->Form->control('q', ['label' => __('Game title'), 'value' => $query, 'placeholder' => __('e.g. The Witcher 3')]) ?>
    <?= $this->Form->button(__('Search')) ?>
    <?= $this->Form->end() ?>

    <?php if ($error): ?>
        <div class="message error"><?= h($error) ?></div>
    <?php endif; ?>

    <?php if ($query !== '' && !$results && !$error): ?>
        <p><?= __('No games found for {0}.', h($query)) ?></p>
    <?php endif; ?>

    <?php if ($results): ?>
        <div class="results-grid">
            <?php foreach ($results as $game): ?>
                <div class="game-result">
                    <?php if ($game['cover_url']): ?>
                        <?= $this->Html->image($game['cover_url'], ['alt' => $game['title']]) ?>
                    <?php endif; ?>
                    <div class="game-body">
                        <h3><?= h($game['title']) ?></h3>
                        <p class="meta"><?= h($game['genres']) ?: __('Unknown genre') ?></p>
                        <p class="meta"><?= __('Rating: {0}', h((string)$game['rating'])) ?></p>
                        <?= $this->Form->create(null, ['url' => ['controller' => 'LibraryGames', 'action' => 'add']]) ?>
                        <?= $this->Form->hidden('rawg_id', ['value' => $game['rawg_id']]) ?>
                        <?= $this->Form->hidden('title', ['value' => $game['title']]) ?>
                        <?= $this->Form->hidden('cover_url', ['value' => $game['cover_url']]) ?>
                        <?= $this->Form->hidden('genres', ['value' => $game['genres']]) ?>
                        <?= $this->Form->hidden('rating', ['value' => $game['rating']]) ?>
                        <?= $this->Form->button(__('Add to library')) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
