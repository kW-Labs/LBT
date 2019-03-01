<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property string $title
 * @property string $platform
 * @property string $action
 * @property string $results
 * @property int $execute_time
 * @property int $total_time
 * @property int $item_id
 * @property int $user_id
 * @property bool $error
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 *
 * @property \App\Model\Entity\Item $item
 */
class Log extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'title' => true,
        'platform' => true,
        'action' => true,
        'results' => true,
        'execute_time' => true,
        'total_time' => true,
        'item_id' => true,
        'user_id' => true,
        'error' => true,
        'created_at' => true,
        'updated_at' => true,
        'item' => true
    ];
}
