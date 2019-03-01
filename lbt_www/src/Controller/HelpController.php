<?php

namespace App\Controller;

use Cake\Event\Event;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HelpController extends AppController
{

    public $title = '';
    public $user_id = '';
    public $action = '';
    public $platform = 'web';
    public $table = 'Help';
    public $item_id = 0;
    public $error = false;
    public $results = '';

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'faq', 'privacy', 'about']);
    }

    public function index()
    {
        return $this->redirect(['action' => 'faq']);
    }

    public function faq()
    {
    }

    public function privacy()
    {
    }

    public function about()
    {
    }


}
