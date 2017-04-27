<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

class PreferencesController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->loadComponent('RequestHandler');
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
        $preferences = $this->Preferences->find('all', ['conditions' => ['Preferences.user_id' => $this->Auth->user('id')],
                                                                         ])->toArray();
        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->getData()); return false;
            $preferences = $this->Preferences->patchEntities($preferences, $this->request->getData());
            $allPrefSaved = true;
            $count = 0;
            foreach ($preferences as $preference) {
                $preference->user_id = $this->Auth->user('id');
                $preference->selected = ($count == 0) ? 1 : (!empty($preference->selected)) ? 1 : 0;
                if($preference->selected == 1 && $this->Preferences->save($preference)) {
                }
                else if($preference->selected == 1) {
                    $allPrefSaved = false;
                    $this->Flash->error(__('Unable to save your preferences.'));
                }
                $count++;
            }
            if($allPrefSaved) {
                $this->Flash->success(__('Your preferences have been saved.'));
                return $this->redirect(['action' => 'add']);
            }
            
            $this->Flash->error(__('Unable to save your preferences.'));
        }
        $this->set('preferences', $preferences);
        $programmes = $this->Preferences->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name')));                               
        $this->set('programmes', $programmes);
        $candidates = $this->Preferences->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name'),
                                                                   'keyField' => 'id',
                                                                   'valueField' => 'type'));
        $testpapers = $this->Preferences->Testpapers->find('list', array('fields' => array('TestpapersProgrammes.testpaper_id','Testpapers.code'),
                                                                   'keyField' => 'TestpapersProgrammes.testpaper_id',
                                                                   'valueField' => 'Testpapers.code'))
                                                    ->innerJoinWith('TestpapersProgrammes', function ($q) {
                                                        return $q->where(['TestpapersProgrammes.testpaper_id' => 'Testpapers.id']);
                                                    });
        $conn = ConnectionManager::get('default');
        $query_string = 'SELECT TestpapersProgrammes.id AS `TestpapersProgrammes__id`, TestpapersProgrammes.testpaper_id AS `TestpapersProgrammes__testpaper_id`, 
                        TestpapersProgrammes.programme_id AS `TestpapersProgrammes__programme_id`,
                        Testpapers.code AS `Testpapers__code`, Programmes.name AS `Programmes__name` 
                        FROM testpapers_programmes TestpapersProgrammes 
                        INNER JOIN testpapers Testpapers 
                        ON (Testpapers.id = TestpapersProgrammes.testpaper_id
                        ) 
                        INNER JOIN programmes Programmes 
                        ON (TestpapersProgrammes.programme_id = Programmes.id 
                        )
                        ORDER BY TestpapersProgrammes.testpaper_id asc, TestpapersProgrammes.programme_id asc';
        $stmt = $conn->execute($query_string);
        $testpapers = $stmt ->fetchAll('assoc');                                           
        //debug($categories->toArray()); return false;
        $this->set('candidates', $candidates);
        //$this->set('testpapers', $testpapers);
        $session = $this->request->session();
        $session->write('papercodemapping', $testpapers);
        $this->set('AuthId', $this->Auth->user('id'));
    }

    public function viewresult()
    {
        //debug(); return false;
        //$this->autoRender = false;
        $testpaperId = $this->request->query['id'];
        $session = $this->request->session();
        $testpapers = $session->read('papercodemapping');
        $progs = [];
        $str = "";
        foreach ($testpapers as $map) {
            if($map['TestpapersProgrammes__testpaper_id'] == $testpaperId) {
                $str .= "<option value=\"" . $map['TestpapersProgrammes__programme_id'] . "\">" . $map['Programmes__name'] . "</option>";
                $progs[$map['TestpapersProgrammes__programme_id']] = $map['Programmes__name'];
            }
        }
        $this->set('data', $str);
        $this->set('_serialize', ['data']);
        //$this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax');
        //exit();
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
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'add' 
                || $this->request->getParam('action') === 'index' || $this->request->getParam('action') === 'viewresult')) {
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
