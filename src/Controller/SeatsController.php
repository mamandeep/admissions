<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

use Cake\ORM\TableRegistry;


class SeatsController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function index() {
        $seats = $this->Seats->find('all', ['contain' => ['Programmes' , 'Categories']]);
        $this->set('seats', $seats);
        $cocs = \Cake\ORM\TableRegistry::get('Cocs');
        $query = $cocs->find('list', ['condition' => ['user_id' => $this->Auth->user('id'),
                                                     'fields'  =>  array('Cocs.id')]]);
        $id = array_keys($query->toArray())[0];
        
        $this->set('centreId', $id);
        $this->set('AuthId', $this->Auth->user('id'));
    }

    public function view($id) {
        $seat = $this->Seats->get($id);
        $this->set(compact('seat'));
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
        $seat = $this->Seats->newEntity();
        if ($this->request->is('post')) {
            $seat = $this->Seats->patchEntity($seat, $this->request->getData());
            // Added this line
            $seat->user_id = $this->Auth->user('id');
            // You could also do the following
            //$newData = ['user_id' => $this->Auth->user('id')];
            //$article = $this->Articles->patchEntity($article, $newData);
            if ($this->Seats->save($seat)) {
                $this->Flash->success(__('Your seat has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your seat.'));
        }
        $this->set('seat', $seat);
        $programmes = $this->Seats->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name')));                               
        $this->set('programmes', $programmes);
        $categories = $this->Seats->Categories->find('list', array('fields' =>array('Categories.id','Categories.type'),
                                                                   'keyField' => 'id',
                                                                   'valueField' => 'type'));
        //debug($categories->toArray()); return false;
        $this->set('categories', $categories);
    }

    public function edit($id = null) {
        $seat = $this->Seats->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $seat = $this->Bookmarks->patchEntity($seat, $this->request->getData());
            $seat->user_id = $this->Auth->user('id');
            if ($this->Seats->save($seat)) {
                $this->Flash->success('The seat has been saved.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The seat could not be saved. Please, try again.');
        }
        $seats = $this->Seats->find('list');
        $this->set(compact('seats'));
    }

    public function delete($id) {
        $this->request->allowMethod(['post', 'delete']);

        $seat = $this->Seats->get($id);
        if ($this->Seats->delete($seat)) {
            $this->Flash->success(__('The seat with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function lockseat($AuthId = null) {
        //$this->request->allowMethod(['post', 'delete']);
        $lockseatsTable = TableRegistry::get('Lockseats');
        $rankingsTable = TableRegistry::get('Rankings');
        $lockedSeat = $lockseatsTable->find('all', ['conditions' => ['Lockseats.user_id' => $this->Auth->user('id')]])->toArray();
        if(count($lockedSeat) > 1) {
            $this->Flash->error('An error has occured while locking seat. Please contact support.');
            return $this->redirect(['action' => 'lockseat']);
        }
        $lockedSeat = (count($lockedSeat) === 0) ? $lockseatsTable->newEntity() : $lockedSeat[0];
        if ($this->request->is(['patch', 'post', 'put']) && !empty($this->request->data()['selected_course'])) {
            $rankingSelected = $rankingsTable->find('all', ['conditions' => ['Rankings.id' => $this->request->data()['selected_course']]])->toArray();
            $rankingSelected = $rankingSelected[0];
            //$rankingSelected->offered_seat = 1;
            $lockSeatData = [];
            $lockSeatData['programme_id'] = $rankingSelected->programme_id;
            $lockSeatData['candidate_id'] = $rankingSelected->candidate_id;
            $lockSeatData['final_category_id'] = $rankingSelected->final_category_id;
            $lockSeatData['rank_id'] = $rankingSelected->id;
            $lockSeatData['user_id'] = $rankingSelected->user_id;
            $lockedSeat = $lockseatsTable->patchEntity($lockedSeat, $lockSeatData);
            if ($lockseatsTable->save($lockedSeat)) {
                $this->Flash->success('The seat has been locked.');
                return $this->redirect(['action' => 'lockseat']);
            } 
            $this->Flash->error('The seat could not be locked. Please contact support.');
        }
        
        $seatOptions = $rankingsTable->find('all', ['conditions' => ['Rankings.user_id' => $this->Auth->user('id')],
                'contain' => ['Programmes', 'Categories']]);
        //debug($seatOptions);
        $this->set('AuthId', $this->Auth->user('id'));
        $this->set('rankings', $seatOptions);
        $this->set('lockedSeatRankId', $lockedSeat->rank_id);
        //debug($lockedSeat->rank_id);
    }
    
    public function allocateseats($id = null) {
        $cocs = \Cake\ORM\TableRegistry::get('Cocs');
        $query = $cocs->find('list', ['condition' => ['user_id' => $this->Auth->user('id'),
                                                     'fields'  =>  array('Cocs.centre_id')]]);
        $id = array_keys($query->toArray())[0];
        
        $seatAllocationTable = TableRegistry::get('Seatallocations');
        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->data()); return false;
            $seatAllocations = $seatAllocationTable->newEntities($this->request->data('Seatallocation'));

            foreach ($seatAllocations as $seat) {
                $seat->user_id = $this->Auth->user('id');
                $seatAllocationTable->save($seat);
            }
        }
            $conn = ConnectionManager::get('default');
            $query_string = 'SELECT Lockseats.id AS `Lockseats__id`, Lockseats.candidate_id AS `Lockseats__candidate_id`, 
                        Lockseats.programme_id AS `Lockseats__programme_id`, Lockseats.final_category_id AS `Lockseats__final_category_id`, 
                        Lockseats.created AS `Lockseats__created`, Lockseats.modified AS `Lockseats__modified`, 
                        Lockseats.user_id AS `Lockseats__user_id`, Lockseats.rank_id AS `Lockseats__rank_id`,
                        Preferences.marks_total AS `Preferences__marks_total`, Programmes.name AS `Programmes__name`,
                        Seats.seat_no AS `Seats__seat_no`, Seats.id AS `Seats__id`, Categories.type AS `Categories__type`,
                        Programmes.centre_id AS `Programmes__centre_id`
                        FROM lockseats Lockseats 
                        INNER JOIN preferences Preferences 
                        ON (Preferences.candidate_id = Lockseats.candidate_id
                                AND Preferences.programme_id = Lockseats.programme_id
                        ) 
                        INNER JOIN programmes Programmes 
                        ON (Lockseats.programme_id = Programmes.id 
                        )
                        INNER JOIN categories Categories 
                        ON (Lockseats.final_category_id = Categories.id 
                        )
                        INNER JOIN seats Seats 
                        ON (Seats.category_id = Lockseats.final_category_id 
                                AND Seats.programme_id = Lockseats.programme_id 
                        )
                        WHERE Lockseats.programme_id in (SELECT Programmes.id AS `Programmes__id` FROM programmes Programmes where Programmes.centre_id = ' . $id . ' ) 
                        AND Seats.id not in (SELECT Seatallocations.seat_id from seatallocations Seatallocations)
                        ORDER BY Lockseats.programme_id asc, Lockseats.final_category_id asc';
            $stmt = $conn->execute($query_string);
        $lockedSeats = $stmt ->fetchAll('assoc');
        //debug($lockedSeats); return false;
        $this->set('lockedSeats', $lockedSeats);
        //$this->set('AuthId', $this->Auth->user('id'));
    }
    
    public function printseats() {
        $cocs = \Cake\ORM\TableRegistry::get('Cocs');
        $query = $cocs->find('list', ['condition' => ['user_id' => $this->Auth->user('id'),
                                                     'fields'  =>  array('Cocs.centre_id')]]);
        $id = array_keys($query->toArray())[0];
        $seatAllocationTable = TableRegistry::get('Seatallocations');
        $seats = $seatAllocationTable->find('all', ['condition' => ['Seatallocations.centre_id' => $id]]);
        
        $this->set('seatallocations', $seats);
    }
    
    
    
    public function centre($id = null) {
        if(!(is_string($id) && ($id = intval(trim($id))))) {
            return false;
        }
        $seats = $this->Seats
            ->find()
            ->contain(['Programmes', 'Categories'])
            ->matching('Programmes', function(\Cake\ORM\Query $q) use ($id) {
                return $q->where(['Programmes.centre_id' => $id]);
            });
        $this->set(compact('seats'));
    }
    
    public function isAuthorized($user = null) {
        if(parent::isAuthorized($user)) {
            return true;
        }
        
        if ($this->request->getParam('action') === 'index') {
            return true;
        }
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'lockseat'
                || $this->request->getParam('action') === 'submitfee' || $this->request->getParam('action') === 'seatalloted')) {
            return true;
        }
        // All users with role as 'exam' can add seats
        if (isset($user['role']) && $user['role'] === 'exam' && ($this->request->getParam('action') === 'add' 
            || $this->request->getParam('action') === 'centre' || $this->request->getParam('action') === 'allocateseats'
            || $this->request->getParam('action') === 'printseats')) {
            return true;
        }

        // The owner of an article can edit and delete it
        if (in_array($this->request->getParam('action'), ['edit', 'delete'])) {
            $seatId = (int) $this->request->getParam('pass.0');
            if ($this->Seats->isOwnedBy($seatId, $user['id'])) {
                return true;
            }
        }
    }
}
