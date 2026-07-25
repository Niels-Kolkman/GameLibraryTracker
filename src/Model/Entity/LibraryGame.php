<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LibraryGame Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $rawg_id
 * @property string $title
 * @property string|null $cover_url
 * @property string|null $genres
 * @property string|null $rating
 * @property int|null $steam_appid
 * @property string $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class LibraryGame extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'rawg_id' => true,
        'title' => true,
        'cover_url' => true,
        'genres' => true,
        'rating' => true,
        'steam_appid' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];
}
