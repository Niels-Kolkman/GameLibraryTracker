<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LibraryGame $libraryGame
 * @var array<int, array<string, mixed>>|null $achievements
 * @var string|null $achievementsError
 * @var int $unlockedCount
 */
?>
<div class="library-games view content">
    <p><?= $this->Html->link(__('&laquo; Back to my library'), ['action' => 'index'], ['escape' => false]) ?></p>

    <div class="game-detail-header">
        <?php if ($libraryGame->cover_url): ?>
            <?= $this->Html->image($libraryGame->cover_url, ['alt' => $libraryGame->title]) ?>
        <?php endif; ?>
        <div>
            <h2><?= h($libraryGame->title) ?></h2>
            <p class="meta"><?= h($libraryGame->genres) ?: __('Unknown genre') ?></p>
            <p class="meta"><?= __('Rating: {0}', h((string)$libraryGame->rating)) ?></p>
            <p><span class="status-badge"><?= h($libraryGame->status) ?></span></p>
        </div>
    </div>

    <h3><?= __('Achievements') ?></h3>

    <?php if ($achievementsError): ?>
        <p class="meta"><?= h($achievementsError) ?></p>
    <?php else: ?>
        <p class="meta"><?= __('{0} / {1} unlocked', $unlockedCount, count($achievements)) ?></p>
        <div class="achievement-list">
            <?php foreach ($achievements as $achievement): ?>
                <div class="achievement <?= $achievement['achieved'] ? 'unlocked' : 'locked' ?>">
                    <?php if ($achievement['icon']): ?>
                        <?= $this->Html->image($achievement['icon'], ['alt' => $achievement['displayName']]) ?>
                    <?php endif; ?>
                    <div>
                        <strong><?= h($achievement['displayName']) ?></strong>
                        <?php if ($achievement['description']): ?>
                            <p class="meta"><?= h($achievement['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
