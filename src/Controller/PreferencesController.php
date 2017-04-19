<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;

class PreferencesController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function index() {
        $preferences = $this->Preferences->find('all', ['condition' => ['Preferences.user_id' => $this->Auth->user('id')], 'contain' => ['Programmes']]);
        $this->set('preferences', $preferences);
    }

    public function view($id) {
        $preference = $this->Preferences->get($id);
        $this->set(compact('candidate'));
    }

    public function sendemail() {
        $email = new Email('default');
        $email->setSender('app@example.com', 'MyApp emailer');
        Email::setConfigTransport('ernet', [
            'host' => 'ssl://mail.eis.ernet.in',
            'port' => 465,
            'username' => 'sa@cup.ac.in',
            'password' => 'ASMann@123#',
            'className' => 'Smtp'
        ]);
        $email->setFrom(['sa@cup.ac.in' => 'My Site'])
                ->setTo('mann.cse@gmail.com')
                ->setSubject('About Link Confirmation')
                ->send('My message');
    }

    public function add() {
        $preference = $this->Preferences->newEntity();
        if ($this->request->is('post')) {
            $preference = $this->Preferences->patchEntity($preference, $this->request->getData());
            // Added this line
            $preference->user_id = $this->Auth->user('id');
            // You could also do the following
            //$newData = ['user_id' => $this->Auth->user('id')];
            //$article = $this->Articles->patchEntity($article, $newData);
            if ($this->Preferences->save($preference)) {
                $this->Flash->success(__('Your preferences have been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to save your preferences.'));
        }
        $this->set('preference', $preference);
        $programmes = $this->Preferences->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name')));                               
        $this->set('programmes', $programmes);
        $candidates = $this->Preferences->Candidates->find('list', array('fields' =>array('Candidates.id','Candidates.name'),
                                                                   'keyField' => 'id',
                                                                   'valueField' => 'type'));
        //debug($categories->toArray()); return false;
        $this->set('candidates', $candidates);
        $this->set('AuthId', $this->Auth->user('id'));
    }

    public function edit($id = null) {
        $preference = $this->Preferences->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $preference = $this->Preferences->patchEntity($preference, $this->request->getData());
            $preference->user_id = $this->Auth->user('id');
            if ($this->Preferences->save($preference)) {
                $this->Flash->success('The preference have been saved.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The preference could not be saved. Please, try again.');
        }
        $programmes = $this->Preferences->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name')));                               
        $this->set('programmes', $programmes);
        $this->set(compact('preference'));
    }

    public function delete($id) {
        $this->request->allowMethod(['post', 'delete']);

        $preference = $this->Preferences->get($id);
        if ($this->Preferences->delete($preference)) {
            $this->Flash->success(__('The preference with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function isAuthorized($user = null) {
        // All users with role as 'exam' can add seats
        if ($this->request->getParam('action') === 'add' || $this->request->getParam('action') === 'index') {
            return true;
        }

        // The owner of an article can edit and delete it
        if (in_array($this->request->getParam('action'), ['edit', 'delete'])) {
            $preferenceId = (int) $this->request->getParam('pass.0');
            if ($this->Preferences->isOwnedBy($preferenceId, $user['id'])) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

}
