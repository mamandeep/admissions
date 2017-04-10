<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;

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
        /*$seats = $this->Seats
            ->find()
            ->contain('Programmes', function(\Cake\ORM\Query $q) {
                return $q->where(['Programmes.centre_id' => $id]);
        })->matching()*/
        
        $this->set('centreId', $id);
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
        if ($this->request->getParam('action') === 'index') {
            return true;
        }
        // All users with role as 'exam' can add seats
        if (isset($user['role']) && $user['role'] === 'exam' && ($this->request->getParam('action') === 'add' 
                || $this->request->getParam('action') === 'centre')) {
            return true;
        }

        // The owner of an article can edit and delete it
        if (in_array($this->request->getParam('action'), ['edit', 'delete'])) {
            $seatId = (int) $this->request->getParam('pass.0');
            if ($this->Seats->isOwnedBy($seatId, $user['id'])) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }
}
