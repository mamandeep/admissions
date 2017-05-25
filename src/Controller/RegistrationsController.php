<?php

namespace App\Controller;

use Cake\Event\Event;

use Cake\ORM\TableRegistry;
use \Datetime;
use DateTimeZone;
use Cake\Auth\DefaultPasswordHasher;

class RegistrationsController extends AppController {
    
    
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'forgotpasswd', 'changepassword']);
    }
    
    public function add()
    {
        $user = $this->Registrations->newEntity();
        //debug($user);
        if ($this->request->is(['post', 'put'])) {
            //debug($this->request->getData());
            $user = $this->Registrations->patchEntity($user, $this->request->getData());
            //debug($user);
            if ($this->Registrations->save($user)) {
                //$this->Auth->setUser($user->toArray());
                $this->Flash->success(__('You have successfully registered.'));
                return $this->redirect([
                    'controller' => 'users',
                    'action' => 'login'
                ]);
            }
            $this->Flash->error(__('Unable to register. Please correct the field values or contact Support.'));
        }
        $this->set('cucetregister', $user);
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
           $session = $this->request->session();
           $session->write('userId',$user['id']);
           $session->write('otp',$this->ozekiOTP());

            $date_now = new DateTime();
            $date_now->setTimezone(new DateTimeZone("Asia/Calcutta"));
            $user = $usersTable->patchEntity($user, ['otp_timestamp' => $date_now->format("Y-m-d H:i:s"),
                                               'otp' => $session->read('otp')], ['validate' => false]);
            
            if(!$usersTable->save($user)) {
                $this->Flash->error(__('There was an error in generating OTP. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }            
            $response = "";
            /*if($this->is_connected()) {
                $response = $this->smsSend($user['mobile'], 'Dear '.$user['first_name'].'! Your One-Time password is: '.$session->read('otp'));
            }
            else {
                $this->Flash->error(__('OTP could not be sent at this time. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }*/
            
            $user = $usersTable->patchEntity($user, ['otp_response' => $response], ['validate' => false]);
            if(!$usersTable->save($user)) {
                $this->Flash->error(__('There was an error in sending OTP. Please contact support.'));
                return $this->redirect(['controller' => 'registrations','action' => 'forgotPassword']);
            }
            $this->Flash->success(__('An OTP has been sent to your Mobile No.'));
            $this->redirect(array('controller' => 'registrations', 'action' => 'changepassword'));   
       }
    }
    
    public function changepassword() {
        if($this->request->is(['post'])) {
            //debug($this->request->data());
            $session = $this->request->session();
            $userId = intval($session->read('userId'));
            $newPassowrd = $this->request->data['password'];
            $password_confirm = $this->request->data['password_confirm'];
            $ozotp = $this->request->data['otp_value'];
            
            
            $usersTable = TableRegistry::get('Users'); 
            $user = $usersTable->find()->where(['id' => $userId])->first();

            if (!$user) {
                $this->Flash->error(__('No user found.'));
                return $this->redirect(['controller' => 'registrations','action' => 'changepassword']);
           }
           $timeGap = $this->getOTPTimeGap($user);
           if( null != $session->read('otp') && $ozotp === $session->read('otp') && ($timeGap <= $this->OTPValidity) && $newPassowrd === $password_confirm) {
               
                if(!$this->checkpassword($newPassowrd, $password_confirm)) {
                    $this->Flash->error(__('New password does not meet the minimum requirements mentioned below.'));
                    return $this->redirect(['controller' => 'registrations', 'action' => 'changepassword']);
                }
                $user = $usersTable->patchEntity($user, ['otp_gap' => $timeGap, 'password' => $newPassowrd], ['validate' => false]);
                if (!$usersTable->save($user)) {
                    $this->Flash->error(__('An error occurred during saving password. Please contact support.'));
                    return $this->redirect(['controller' => 'registrations', 'action' => 'changepassword']);
                }
            }
            else if($ozotp !== $session->read('otp')) {
                $this->Flash->error(__('The OTP entered is not valid.'));
                if($timeGap > $this->OTPValidity)
                    $session->delete('otp');
                return $this->redirect(['controller' => 'registrations', 'action' => 'forgotpasswd']);
            }
            else if($newPassowrd !== $password_confirm) {
                $this->Flash->error(__('The passwords did not match.'));
                return $this->redirect(['controller' => 'registrations', 'action' => 'forgotpasswd']);
            }
            else {
                $this->Flash->error(__('There was an error in changing the password. Please contact support.'));
                return $this->redirect(['controller' => 'registrations', 'action' => 'forgotpasswd']);
            }
            $this->Flash->success(__('You have successfully changed password.'));
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }
    
    private function checkpassword($newPassword, $passwordConfirm) {
        $match = true;
        
        $match = (strlen($newPassword) >= 8) ? true : false;
        if($match === false) {
            return $match;
        }
        //debug($match);
        $pattern = '/[0-9]+/';
        $match = (preg_match($pattern, $newPassword)) ? true : false;
        if($match === false) {
            return $match;
        }
        //debug($match);
        $pattern = '/[a-zA-Z]+/';
        $match = (preg_match($pattern, $newPassword)) ? true : false;
        if($match === false) {
            return $match;
        }
        //debug($match);
        $match = (strcmp($newPassword,$passwordConfirm) === 0);
        //debug($match);
        return $match;
    }
}