<?php

namespace App\Controller;

use Cake\Event\Event;

use Cake\ORM\TableRegistry;

class RegistrationsController extends UsersController {
    
    
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'forgotpasswd', 'changepassword']);
    }
    
    public function add()
    {
        $user = $this->Registrations->newEntity();
        if ($this->request->is('post')) {
            debug($this->request->getData());
            $user = $this->Registrations->patchEntity($user, $this->request->getData());
            debug($user);
            if ($this->Registrations->save($user)) {
                //$this->Auth->setUser($user->toArray());
                $this->Flash->success(__('You have successfully registered.'));
                return $this->redirect([
                    'controller' => 'users',
                    'action' => 'login'
                ]);
            }
            $this->Flash->error(__('Unable to register. Contact support.'));
        }
        $this->set('user', $user);
    }
    
    public function forgotpasswd()
    {
        if($this->request->is(['post'])) {
            $email = $this->request->data['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->Flash->error(__('Email Id is not in correct format.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }
            
            $usersTable = TableRegistry::get('Users'); 
            $user = $usersTable->find()->where(['email' => $email ])->first();

            if (!$user) {
                $this->Flash->error(__('No user with that email found.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
           }
           $_SESSION['userId'] = $user['id'];
           $_SESSION['otp'] = $this->ozekiOTP();

            $date_now = new DateTime();
            $date_now->setTimezone(new DateTimeZone("Asia/Calcutta"));
            $user = $usersTable->patchEntity($user, ['otp_timestamp' => $date_now->format("Y-m-d H:i:s"),
                                               'otp' => $_SESSION['otp']], ['validate' => false]);
            
            if(!$usersTable->save($user)) {
                $this->Flash->error(__('There was an error in generating OTP. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }            
            $response = "";
            if($this->is_connected()) {
                $response = $this->smsSend($user['mobile'], 'Dear '.$user['first_name'].'! Your One-Time password is: '.$_SESSION['otp']);
            }
            else {
                $this->Flash->error(__('OTP could not be sent at this time. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }
            
            $user = $user->patchEntity($user, ['otp_response' => $response], ['validate' => false]);
            if(!$usersTable->save($user)) {
                $this->Flash->error(__('There was an error in sending OTP. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }
            
            $this->redirect(array('controller' => 'registrations', 'action' => 'changepassword'));   
       }
    }
    
    public function changepassword() {
        if($this->request->is(['post'])) {
            $userId = intval($_SESSION['userId']);
            $newPassowrd = $this->request->data['passowrd'];
            $password_confirm = $this->request->data['passowrd_confirm'];
            
            
            $usersTable = TableRegistry::get('Users'); 
            $user = $usersTable->find()->where(['id' => $userId])->first();

            if (!$user) {
                $this->Flash->error(__('No user found.'));
                return $this->redirect(['controller' => 'registrations','action' => 'changepassword']);
           }
           $ozotp = $user['otp'];
           $timeGap = $this->getOTPTimeGap($user);
           if( isset($_SESSION['otp']) && $ozotp === $_SESSION['otp'] && ($timeGap <= $this->OTPValidity) && $newPassowrd === $password_confirm) {
               
                $hasher = new DefaultPasswordHasher();
                $val = $hasher->hash($newPassowrd);
                $user = $usersTable->patchEntity($user, ['otp_gap' => $timeGap, 'password' => $val], ['validate' => false]);
                if (!$usersTable->save($user)) {
                    $this->Flash->error(__('An error occurred during saving password. Please contact support.'));
                    return $this->redirect(['controller' => 'registrations', 'action' => 'changepassword']);
                }
            }
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }
}