<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;

class CandidatesController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function index() {
        //$candidates = $this->Candidates->find('all', ['contain' => ['Seats']]);
        //$this->set('candidates', $candidates);
        //debug($seats->toArray()); return null;
    }

    public function view($id) {
        $candidate = $this->Candidates->get($id);
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
        $candidate = $this->Candidates->newEntity();
        $exisiingcandidate = $this->Candidates->find('all', ['condition' => ['user_id' => $this->Auth->user('id')]]);
        if(count($exisiingcandidate)) {
            $this->Flash->success(__('You have already submitted the application form.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            $candidate = $this->Candidates->patchEntity($candidate, $this->request->getData());
            // Added this line
            $candidate->user_id = $this->Auth->user('id');
            // You could also do the following
            //$newData = ['user_id' => $this->Auth->user('id')];
            //$article = $this->Articles->patchEntity($article, $newData);
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success(__('Your application form has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to save your application form.'));
        }
        $this->set('candidate', $candidate);
    }

    public function edit($id = null) {
        $candidate = $this->Candidates->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $candidate = $this->Candidates->patchEntity($candidate, $this->request->getData());
            $candidate->user_id = $this->Auth->user('id');
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success('The application form has been saved.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The application form could not be saved. Please, try again.');
        }
        $this->set(compact('candidate'));
    }

    public function delete($id) {
        $this->request->allowMethod(['post', 'delete']);

        $seat = $this->Seats->get($id);
        if ($this->Seats->delete($seat)) {
            $this->Flash->success(__('The seat with id: {0} has been deleted.', h($id)));
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
            $candidateId = (int) $this->request->getParam('pass.0');
            if ($this->Candidates->isOwnedBy($candidateId, $user['id'])) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

}
