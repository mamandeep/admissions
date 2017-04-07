<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class RegistrationsController extends UsersController {
    
    
    public function add()
    {
        $user = $this->Registrations->newEntity();
        if ($this->request->is('post')) {
            debug($this->request->getData());
            $user = $this->Registrations->patchEntity($user, $this->request->getData());
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
}