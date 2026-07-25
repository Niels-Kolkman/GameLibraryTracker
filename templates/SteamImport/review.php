<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array<string, mixed>> $results
 */
$matchedCount = count(array_filter($results, fn ($r) => $r['rawg'] !== null && !$r['already_in_library']));
?>
<div class="steam-import review content">
    <h2><?= __('Review your Steam library') ?></h2>
    <p class="meta">
        <?= __('Found {0} game(s). {1} matched and ready to import — review the selection below.', count($results), $matchedCount) ?>
    </p>

    <?= $this->Form->create(null, ['url' => ['action' => 'import'], 'class' => 'wide-form']) ?>
    <div class="results-grid">
        <?php foreach ($results as $game): ?>
            <div class="game-result">
                <?= $this->Html->image($game['rawg']['cover_url'] ?? $game['header_image'], ['alt' => $game['name']]) ?>
                <div class="game-body">
                    <h3><?= h($game['rawg']['title'] ?? $game['name']) ?></h3>
                    <?php if ($game['rawg']): ?>
                        <p class="meta"><?= h($game['rawg']['genres']) ?: __('Unknown genre') ?></p>
                        <?php if ($game['already_in_library']): ?>
                            <p class="meta"><span class="status-badge"><?= __('Already in library') ?></span></p>
                        <?php else: ?>
                            <label>
                                <?= $this->Form->checkbox('appids[]', [
                                    'value' => $game['appid'],
                                    'checked' => true,
                                    'hiddenField' => false,
                                ]) ?>
                                <?= __('Add to library') ?>
                            </label>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="meta"><?= __('No confident RAWG match found.') ?></p>
                        <?= $this->Html->link(
                            __('Search manually'),
                            ['controller' => 'Games', 'action' => 'search', '?' => ['q' => $game['name']]],
                        ) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $this->Form->button(__('Import selected')) ?>
    <?= $this->Form->end() ?>
</div>
