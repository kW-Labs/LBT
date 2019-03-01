<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Utility\Text;
use Cake\Mailer\Email;
use Cake\Routing\Router;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property bool|object Global
 * @property bool|object BPD
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public $title = '';
    public $user_id = '';
    public $action = '';
    public $platform = 'web';
    public $table = 'Users';
    public $item_id = 0;
    public $error = false;
    public $results = '';

    public function isAuthorized($user = null)
    {
        $action = $this->request->getParam('action');
        if (in_array($action, ['profile', 'welcome', 'timezone', 'language', 'changePassword', 'changeEmail'])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['thanks', 'confirm', 'logout', 'forgotPassword', 'reset']);
    }

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('BPD');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $users = $this->paginate($this->Users);
        $this->set(compact('users'));
    }


    public function register()
    {
        $this->viewBuilder()->setLayout('blank');
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            $key = Text::uuid();
            $user->hash = $key;
            $user_email =  $user->email = strtolower ($this->request->getData('email'));

            $saved_user = $this->Users->save($user);
            if ($saved_user) {

                $savedUser = $this->Users->save($user);
                if ($user->getErrors()) {
                    // Entity failed validation.
                    if (count($user->getErrors()) > 0) {
                        $v_string = '';
                        $v_error = [];
                        foreach ($user->getErrors() as $field => $error) {
                            foreach ($error as $k => $v) {
                                if (!in_array($v, $v_error)) {
                                    $v_string .= $v;
                                    array_push($v_error, $v);
                                }
                            }
                        }

                        $this->Flash->error($v_string);
                    }
                    return $this->redirect(['action' => 'register']);
                } else {
                    if ($savedUser) {

                        // Send Welcome email
                        $url = Router::url(['controller' => 'users', 'action' => 'confirm','_ssl' =>true], true) . '/' . $key;
                        $email = new Email();
                        $email
                            ->setTemplate('welcome')
                            ->setEmailFormat('html')
                            ->setSubject('Welcome to LBT')
                            ->setViewVars(['email' => $user_email, 'url' => $url])
                            ->setTo($user_email)
                            ->send();

                        // Log
                        $this->title = __('Send Welcome Email');
                        $this->results = __('Email sent');
                        $this->action = __('Send Email');
                        $this->item_id = $savedUser->id;
                        $this->user_id = $savedUser->id;
                        $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                        $this->Flash->success($this->results);
                    } else {

                        $this->results = __('The user could not be saved. Please, try again.');
                        $this->Flash->error($this->results);
                    }
                }

                return $this->redirect(['action' => 'thanks']);
            }

            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        return null;
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        return null;
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function login()
    {
        $this->viewBuilder()->setLayout('login');
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {

                // User must confirm email
                if (!$user['confirm']) {
                    $this->Flash->error(__('Please check your email to confirm your account'));
                } else if (!$user['active']) {
                    $this->Flash->error(__('Your account has been disabled. Please email the Administrator to re-enable it.'));
                } else {

                    // Set user
                    $this->Auth->setUser($user);

                    // Log
                    $this->title = __("Logged in");
                    $this->action = __("Auth");
                    $this->user_id = isset($user['id']) ? $user['id'] : 0;
                    $this->item_id = isset($user['id']) ? $user['id'] : 0;
                    $this->results = __('Opened user session');
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);

                    // Redirect User
                    $redirectUri = $this->Auth->redirectUrl();
                    return $this->redirect($redirectUri);
                }
            } else {

                if(preg_match('/[A-Z]/', $this->request->getData('email'))) {
                    $this->Flash->set(__('Username must be all lowercase'));
                }else{
                    $this->Flash->set(__('Username or password is incorrect'));
                }
            }
        }
        return null;
    }

    public function thanks()
    {
        $this->viewBuilder()->setLayout('thanks');
    }



    public function confirm($key)
    {
        $user = $this->Users->find('all', ['conditions' => ['hash' => $key]])->first();
        $user_id = $user['id'];
        $this->user_id = $user_id;
        $this->title = __("Confirm Email");
        $this->action = __("Confirm Email");

        // Confirmation already set
        if ($user) {
            $user = $this->Users->get($user->id);
            $confirm = isset($user->confirm) ? $user->confirm : false;
            if ($confirm) {
                return $this->redirect(['action' => 'login']);
            } else {

                // API Key
                $apiJson = json_decode($this->BPD->getApiKeyFromBpd($user->email),true);

                // Update User
                $user->confirm = true;
                $user->active = true;
                $user->hash = null;
                $user->api_key = isset($apiJson['api_key']) ? $apiJson['api_key']: "";

                $savedUser = $this->Users->save($user);
                if ($savedUser && !empty($user->api_key)) {

                    // Log
                    $this->user_id = $savedUser->id;
                    $this->item_id = $savedUser->id;
                    $this->results = __("Email Confirmed. Please Login");
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                    $this->Flash->success($this->results);

                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->error = 1;
                    $this->results = __('Email Could not be Confirmed. Please, try again.');
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                    $this->Flash->error($this->results);
                }
            }
        } else {
            $this->error = 1;
            $this->results = __("Oops!");
            $this->Flash->error($this->results);
            return $this->redirect(['action' => 'login']);
        }
        return null;
    }

    public function logout()
    {

        // Delete Dealer Info
        $user_id = !empty($this->Auth->user('id')) ? $this->Auth->user('id') : 0;

        // Log entry
        $this->title = __("Logged out");
        $this->action = __("Auth");
        $this->user_id = $user_id;
        $this->item_id = $user_id;
        $this->results = __('Closed user session');
        $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);

        $this->Auth->logout();
        return $this->redirect(['controller' => 'Buildings','action' => 'home']);
    }

    public function profile()
    {
        $email = $this->Auth->user('email');
        $this->set(compact('email'));
    }

    public function changeEmail()
    {
        $email = $this->Auth->user('email');
        $user_id = $this->Auth->user('id');
        $user = $this->Users->get($user_id);
        $this->user_id = $user_id;
        $this->item_id = $user_id;
        $this->title = __("Update Email");
        $this->action = __("Edit");

        // Submitted data?
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Validate Password
            $user = $this->Users->get($this->Auth->user('id'));
            $userData = $this->Users->patchEntity($user, [
                'old_password' => $this->request->getData('password'),
                'password' => $this->request->getData('password')
            ],
                ['validate' => 'password']
            );

            // Patch New Email
            if ($email !== $this->request->getData('email')) {
                if (count($userData->getErrors()) > 0) {
                    $v_string = '';
                    $v_error = [];
                    foreach ($user->getErrors() as $field => $error) {
                        foreach ($error as $k => $v) {
                            if (!in_array($v, $v_error)) {
                                $v_string .= $v;
                                array_push($v_error, $v);
                            }
                        }
                    }

                    $this->error = 1;
                    $this->results = $v_string;
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                    $this->Flash->error($this->results);
                } else {
                    $userData->email = $this->request->getData('email');

                    // Try and save the request data
                    $saved_user = $this->Users->save($userData);
                    if (!$saved_user) {
                        $this->error = 1;
                        $this->results = __('The user could not be saved. Please, try again.');
                        $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                        $this->Flash->error($this->results);
                        return $this->redirect(['action' => 'profile']);
                    } else {

                        $this->item_id = $saved_user->id;
                        $this->results = __('New Email address has been saved');
                        $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                        $this->Flash->success($this->results);

                        // Force Session Update
                        $data = $userData->toArray();
                        unset($data['password']);
                        $this->Auth->setUser($data);
                        return $this->redirect(['action' => 'profile']);
                    }
                }
            } else {
                // Reset password
                $data = $this->request->getData();
                unset($data['password']);
                $this->request = $this->request->withParsedBody($data);
                $this->error = 1;
                $this->results = __('Email address is the same');
                $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                $this->Flash->error($this->results);
            }
        }

        $this->set(compact('email', 'user'));
        return null;
    }


    public function changePassword()
    {
        $user_id = $this->Auth->user('id');
        $user = $this->Users->get($user_id);
        $this->user_id = $user_id;
        $this->title = __("Update Password");
        $this->action = __("Edit");

        if (!empty($this->request->getData())) {
            $user = $this->Users->patchEntity($user, [
                'old_password' => $this->request->getData('old_password'),
                'password' => $this->request->getData('password1'),
                'password1' => $this->request->getData('password1'),
                'password2' => $this->request->getData('password2')
            ],
                ['validate' => 'password']
            );

            if (count($user->getErrors()) > 0) {
                $v_string = '';
                $v_error = [];
                foreach ($user->getErrors() as $field => $error) {
                    foreach ($error as $k => $v) {
                        if (!in_array($v, $v_error)) {
                            $v_string .= $v;
                            array_push($v_error, $v);
                        }
                    }
                }

                $this->error = 1;
                $this->results = $v_string;
                $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                $this->Flash->error($this->results);
                return $this->redirect(['action' => 'change_password']);

            } else {
                $saved_user = $this->Users->save($user);
                if (!$saved_user) {
                    $this->error = 1;
                    $this->results = __('The user could not be saved. Please, try again.');
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                    $this->Flash->error($this->results);
                    return $this->redirect(['action' => 'change_password']);
                } else {
                    $this->results = __('The password was successfully changed');
                    $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                    $this->Flash->success($this->results);
                }
            }

            return $this->redirect(['action' => 'profile']);
        }

        $this->set('user', $user);
        return null;
    }

    public function forgotPassword()
    {
        $this->title = __("Forgot Password");
        $this->action = __("Send Email");
        if ($this->request->is('post')) {
            if (!empty($this->request->getData())) {
                if (empty($this->request->getData('email'))) {
                    $this->Flash->error('Please Provide Your Email Address that You used to Register with Us');
                } else {
                    $email = strtolower($this->request->getData('email'));

                    //Check if the Email exist
                    $user = $this->Users->find('all', ['conditions' => ['Users.email' => $email]])->first();
                    if (!empty($user)) {

                        // create a unique key and hashing it only for on time use.
                        $key = uniqid();
                        $hash = md5($key);

                        // create the url with the reset function
                        $url = Router::url(['controller' => 'users', 'action' => 'reset','_ssl' =>true], true) . '/' . $key . '#' . $hash;
                        $ms = $url;
                        $ms = wordwrap($ms, 1000);
                        $user = $this->Users->get($user->id);
                        $user->token = $key;

                        if ($this->Users->save($user)) {
                            $Email = new Email();
                            $Email
                                ->setTemplate('forgot_password')
                                ->setTo($user['email'])
                                ->setSubject('Reset Password - LBT')
                                ->setEmailFormat('html')
                                ->setViewVars(['ms' => $ms]);
                            $this->user_id = $user['id'];
                            $this->item_id = $user['id'];

                            if ($Email->send()) {
                                $this->results = __('Please check your email to reset your password.');
                                $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                                $this->Flash->success($this->results);
                            } else {
                                $this->error = 1;
                                $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                                $this->Flash->error($this->results);
                            }
                        }
                    } else {
                        $this->Flash->error(__('Email does Not Exist'));
                    }
                }
            }

            return $this->redirect(['action' => 'login']);
        }
        return null;
    }

    public function reset($token=null){
        $this->title = __("Email Token Password Reset");
        $this->action = __("Edit");
        $this->viewBuilder()->setLayout('blank');

        if(!empty($token)){
            $u = $this->Users->find('all', ['conditions' => ['Users.token' => $token]])->first();
            $user = $this->Users->get($u['id']);
            $this->user_id = $u['id'];
            $this->item_id = $u['id'];

            if($u){
                if(!empty($this->request->getData())){
                    $user = $this->Users->patchEntity($user, [
                        'password'      => $this->request->getData('password1'),
                        'password1'     => $this->request->getData('password1'),
                        'password2'     => $this->request->getData('password2')
                    ],
                        ['validate' => 'reset']
                    );

                    // Update Hash
                    $new_hash=sha1($u['email'].rand(0,100));
                    $user->token =$new_hash;
                    if(count($user->getErrors())>0){
                        $v_string = '';
                        $v_error = [];
                        foreach ($user->getErrors() as $field => $error) {
                            foreach ($error as $k => $v) {
                                if (!in_array($v, $v_error)) {
                                    $v_string .= $v;
                                    array_push($v_error, $v);
                                }
                            }
                        }

                        $this->error = 1;
                        $this->results = $v_string;
                        $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                        $this->Flash->error($v_string);
                    }else {
                        // Try and save the request data
                        $saved_user = $this->Users->save($user);
                        if (!$saved_user) {
                            $this->error = 1;
                            $this->results = __('The password could not be changed');
                            $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                            $this->Flash->error($this->results);
                            return $this->redirect(['action' => 'login']);
                        } else {
                            // Save Results
                            $this->item_id = $saved_user->id;
                            $this->results = __('The password was successfully changed');
                            $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                            $this->Flash->success($this->results);
                            return $this->redirect(['action' => 'login']);
                        }
                    }
                }
            }
            else {
                $this->error = 1;
                $this->results = __('The reset token can only be used once.');
                $this->Global->saveLog($this->user_id, $this->title, $this->item_id, $this->platform, $this->table, $this->action, $this->error, $this->results);
                $this->Flash->error( $this->results);
                return $this->redirect(['action' => 'login']);
            }
        }
        else{
            return $this->redirect(['action' => 'login']);
        }

        $this->set('token',$token);
        return null;
    }

}
