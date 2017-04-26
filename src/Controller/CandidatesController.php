<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class CandidatesController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function index() {
        $this->set('AuthId', $this->Auth->user('id'));
    }

    public function view($id) {
        $candidate = $this->Candidates->get($id);
        $this->set(compact('candidate'));
        $this->set('AuthId', $this->Auth->user('id'));
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
        $candidate = $this->Candidates->find('all', ['conditions' => ['Candidates.user_id' => $this->Auth->user('id')],
                                                     'contain' => ['Categories']])->toArray();
        //debug($candidate); //return false;
        if(count($candidate) > 1) {
            $this->Flash->error(__('More than 1 application forms have been received. Please contact support.'));
            return $this->redirect(['action' => 'add']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $candidate = $candidate[0];
            $candidate = $this->Candidates->patchEntity($candidate, $this->request->getData());
            // Added this line
            $candidate->user_id = $this->Auth->user('id');
            // You could also do the following
            //$newData = ['user_id' => $this->Auth->user('id')];
            //$article = $this->Articles->patchEntity($article, $newData);
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success(__('Your application form has been saved.'));
                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('Unable to save your application form.'));
        }
        $this->set('candidate', $candidate[0]);
        $categories = $this->Candidates->Categories->find('all')
                                                              ->where(['Categories.id !=' => 1]);
        $catOptions = [];
        foreach ($categories as $category) {
            //debug($category);
            $catOptions[$category['id']] = $category['type'];
        }
        
        $this->set('categories', $catOptions);
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
    
    public function seatalloted($id = null) {
        $candidatesTable = TableRegistry::get('Candidates');
        $candidate = $candidatesTable->find('list')->where(['Candidates.user_id' => $this->Auth->user('id')]);
        
        //debug($candidate->toArray()); return false;
        $candidateId = array_keys($candidate->toArray())[0];
        //$seatAllocationTable = TableRegistry::get('Seatallocations');
        $query = 'SELECT Seatallocations.id AS `Seatallocations__id`, Seatallocations.candidate_id AS `Seatallocations__candidate_id`, 
                        Seatallocations.seat_id AS `Seatallocations__seat_id`, Seatallocations.user_id AS `Seatallocations__user_id`, 
                        Seatallocations.created AS `Seatallocations__created`, Seatallocations.modified AS `Seatallocations__modified`, 
                        Seatallocations.centre_id AS `Seatallocations__centre_id`,
                        Programmes.name AS `Programmes__name`, Categories.type AS `Categories__type`
                        FROM seatallocations Seatallocations 
                        INNER JOIN seats Seats 
                        ON (Seatallocations.seat_id = Seats.id
                        ) 
                        INNER JOIN programmes Programmes 
                        ON (Seats.programme_id = Programmes.id 
                        )
                        INNER JOIN categories Categories 
                        ON (Seats.category_id = Categories.id 
                        )
                        WHERE Seatallocations.candidate_id = ' . $candidateId;
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute($query);
        $allocatedSeats = $stmt ->fetchAll('assoc');
        $this->set('allocatedSeats', $allocatedSeats);
    }
    
    public function submitfee() {
    }

    public function isAuthorized($user = null) {
        // All users with role as 'exam' can add seats seatalloted
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'add' 
                || $this->request->getParam('action') === 'index' || $this->request->getParam('action') === 'seatalloted')) {
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
