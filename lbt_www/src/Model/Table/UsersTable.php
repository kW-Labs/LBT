<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Users Model
 *
 * @property |\Cake\ORM\Association\HasMany $Logs
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Logs', [
            'foreignKey' => 'user_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->add('email',['unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'Email is already registered']])
            ->notEmpty('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 245)
            ->add('password', 'validFormat',[ 'rule' => array('custom', '/(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*/i'),
                'message' => 'Password must contain at least 8 characters and include at least 3 of the following 4 character types: (1) Upper case character, (2) Lower case character, (3) Numeric digit, (4) Special character'
            ])
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->scalar('role')
            ->maxLength('role', 20)
            ->allowEmpty('role');

        $validator
            ->scalar('api_key')
            ->maxLength('api_key', 245)
            ->allowEmpty('api_key');

        $validator
            ->scalar('hash')
            ->maxLength('hash', 245)
            ->allowEmpty('hash');

        $validator
            ->scalar('token')
            ->maxLength('token', 245)
            ->allowEmpty('token');

        $validator
            ->scalar('password_reset_hash')
            ->maxLength('password_reset_hash', 128)
            ->allowEmpty('password_reset_hash');

        $validator
            ->dateTime('password_reset_request_date')
            ->allowEmpty('password_reset_request_date');

        $validator
            ->boolean('active')
            ->allowEmpty('active');

        $validator
            ->boolean('confirm')
            ->allowEmpty('confirm');

        $validator
            ->dateTime('created_at')
            ->allowEmpty('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmpty('updated_at');

        return $validator;
    }


    public function validationReset(Validator $validator )
    {
        $validator
            ->add('password1',[
                'match'=>[
                    'rule'=> ['compareWith','password2'],
                    'message'=>'The passwords do not match!',
                ]
            ])
            ->notEmpty('password1');
        $validator
            ->add('password2',[
                'match'=>[
                    'rule'=> ['compareWith','password1'],
                    'message'=>'The passwords do not match!',
                ]
            ])
            ->add('password2', 'validFormat',[ 'rule' => array('custom', '/(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*/i'),
                'message' => 'Password must contain at least 8 characters and include at least 3 of the following 4 character types: (1) Upper case character, (2) Lower case character, (3) Numeric digit, (4) Special character'
            ])
            ->notEmpty('password2');
        return $validator;
    }

    public function validationPassword(Validator $validator )
    {
        $validator
            ->add('old_password','custom',[
                'rule'=>  function($value, $context){
                    $user = $this->get($context['data']['id']);
                    if ($user) {
                        if ((new DefaultPasswordHasher)->check($value, $user->password)) {
                            return true;
                        }
                    }
                    return false;
                },
                'message'=>'The old password does not match the current password!',
            ])
            ->notEmpty('old_password');
        $validator
            ->add('password1',[
                'match'=>[
                    'rule'=> ['compareWith','password2'],
                    'message'=>'The passwords do not match!',
                ]
            ])
            ->notEmpty('password1');
        $validator
            ->add('password2',[
                'match'=>[
                    'rule'=> ['compareWith','password1'],
                    'message'=>'The passwords do not match!',
                ]
            ])
            ->add('password2', 'validFormat',[ 'rule' => array('custom', '/(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*/i'),
                'message' => 'Password must contain at least 8 characters and include at least 3 of the following 4 character types: (1) Upper case character, (2) Lower case character, (3) Numeric digit, (4) Special character'
            ])
            ->notEmpty('password2');
        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
