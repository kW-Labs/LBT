<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $api_key
 * @property string $hash
 * @property string $token
 * @property string $password_reset_hash
 * @property \Cake\I18n\FrozenTime $password_reset_request_date
 * @property bool $active
 * @property bool $confirm
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 */
class User extends Entity
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
        'email' => true,
        'password' => true,
        'role' => true,
        'api_key' => true,
        'hash' => true,
        'token' => true,
        'password_reset_hash' => true,
        'password_reset_request_date' => true,
        'active' => true,
        'confirm' => true,
        'created_at' => true,
        'updated_at' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
        return false;
    }
}
