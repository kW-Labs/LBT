<?php
namespace App\Shell;
use Cake\Console\Shell;
use App\Controller\Component\BPDComponent;
use Cake\Controller\ComponentRegistry;

class ImportUsersShell extends Shell
{
    public $BPDComponent;

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
        $this->BPDComponent = new BPDComponent(new ComponentRegistry(), []);

        ini_set('memory_limit', '250M');
    }

    public function import(){
        $users = ["jcorona@kw-engineering.com"];
        foreach ($users as $email) {
            // If user does not exist, create new user
            $user_id = $this->Users->find('all', ['conditions' => ['email' => $email]])->first();
            if(empty($user_id)) {

                // New User
                $user = $this->Users->newEntity();
                $apiJson = json_decode($this->BPDComponent->getApiKeyFromBpd($email), true);
                $apiKey = isset($apiJson['api_key']) ? $apiJson['api_key']: "";
                $newUser = ['api_key' => $apiKey,
                    'confirm' => true,
                    'active' => true,
                    'hash'  => null,
                    'password' => $this->randomPassword(),
                    'email' => $email];
                $user = $this->Users->patchEntity($user, $newUser);
                $saved = $this->Users->save($user);
                if($saved){
                    $this->log("Created: " . $email);
                }
            }
        }
    }

    public function update(){
        $users = $this->Users->find('all');
        foreach ($users as $user) {

            $apiJson = json_decode($this->BPDComponent->getApiKeyFromBpd($user->email), true);
            $new_api_key = isset($apiJson['api_key']) ? $apiJson['api_key'] : "";
            if($user->api_key !== $new_api_key) {
                $user->api_key = $new_api_key;
                $this->Users->save($user);
            }
            
        }
    }

    private function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

}