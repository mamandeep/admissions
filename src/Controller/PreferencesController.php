<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

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
                                                        'order' => ['Preferences.id' => 'ASC']
                                                                         ])->toArray();
        $candidate = $this->Preferences->Candidates->find('all', array('fields'     => array('Candidates.id'),
                                                                        'conditions' => ['Candidates.user_id' => $this->Auth->user('id')]))->first();
																			
        if(count($candidate) === 0) {
            $this->Flash->error(__('Please fill your application form before filling the preferences.'));
            return $this->redirect(['controller' => 'candidates', 'action' => 'add']);
        }
        $flag = $this->isPreferenceFillingOpen();
        if ($this->request->is(['patch', 'post', 'put']) && $flag === true) {
            if(!$this->checkOrder($this->request->getData())) {
                $this->Flash->error(__('The preferences selected are not in order.'));
                return $this->redirect(['action' => 'add']);
            }
            $allPrefSaved = true;
            $count = 0;
            $preference = "";
            $validationErrosPresent = false;
            for ($count=0; $count < 3; $count++ ) {
                $pref_data = $this->request->getData()[$count];
                //debug($this->request->getData()); return false;
                if(($count == 0) ? 1 : (!empty($pref_data['selected'])) ? 1 : 0) {
                    $preference = $this->Preferences->newEntity($pref_data);
                    $validationErrosPresent = !empty($preference->errors()) ? true : false;
                    //debug($preference); return false;
                    $preference = $this->Preferences->patchEntity($preference, ['user_id' => $this->Auth->user('id'), 'selected' => 1], ['validate' => false]);
                }
                else {
                    $newData = [];
                    $newData['id'] = $pref_data['id'];
                    $newData['candidate_id'] = $pref_data['candidate_id'];
                    $newData['programme_id'] = NULL;
                    $newData['testpaper_id'] = 0;
                    $newData['marks_A'] = NULL;
                    $newData['marks_B'] = NULL;
                    $newData['marks_total'] = NULL;
                    $newData['user_id'] = $this->Auth->user('id');
                    $newData['selected'] = 0;
                    $preference = $this->Preferences->newEntity($newData, ['validate' => false]);
                }
                $preferences[$count] = $preference;
            }
            //debug($preferences); 
            if(!$validationErrosPresent) {
                try {   
                    foreach($preferences as $preferenceObj) {
			//debug($preferenceObj->isNew()); 
                        if($preferenceObj->isNew() && $this->Preferences->save($preferenceObj)) {
                        }
                        else if($preferenceObj->isNew() && $preference->selected == 1) {
                            $allPrefSaved = false;
                        }
                    }
                } catch (\Exception $e) {
                    $allPrefSaved = false;
                }
            }
            if(!$validationErrosPresent && $allPrefSaved) {
                $this->Flash->success(__('Your preferences have been saved.'));
                return $this->redirect(['action' => 'add']);
            }
            //debug($validationErrosPresent ); debug($allPrefSaved); return null;
            $this->Flash->error(__('Unable to save your preferences.'));
        }
        else if($this->request->is(['patch', 'post', 'put']) && $flag === false) {
            $this->Flash->error(__('Preference filling is closed at this time.'));
        }
        if(count($preferences) == 0) {
            $preferences = [];
            $preferences[0] = $this->Preferences->newEntity();
            $preferences[1] = $this->Preferences->newEntity();
            $preferences[2] = $this->Preferences->newEntity();
        }
        $this->set('preferences', $preferences);
        $programmes = $this->Preferences->Programmes->find('list', array('fields' =>array('Programmes.id','Programmes.name')));                               
        $this->set('programmes', $programmes);
        $candidate = $this->Preferences->Candidates->find('all', array('fields'     => array('Candidates.id'),
                                                                        'conditions' => ['Candidates.user_id' => $this->Auth->user('id')]))->first();
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
        $this->set('candidate', $candidate);
        $cucetdata = [];
	
        if($flag === true) {
            $query_string = 'select t1.id as testpaper_id, u1.id as user_id, c2.vTestPaperCode as cucet_papercode,
                            c2.Part_A as cucet_marks_A, c2.Part_B as cucet_marks_B, c2.TOTAL_MARKS as cucet_total_marks 
                            from candidates as c1
                            inner join users as u1
                            on c1.user_id = u1.id
                            inner join cucet_result as c2
                            on u1.username = c2.ApplicationID
                            inner join testpapers as t1
                            on t1.code = c2.vTestPaperCode
                            where u1.id = ' . $this->Auth->user('id') . '
                            group by c2.vTestPaperCode';
            $stmt = $conn->execute($query_string);
            $cucetdata = $stmt->fetchAll('assoc');
        }
        $filesTable = TableRegistry::get('Uploadfiles'); 
        $file = $filesTable->find()->where(['Uploadfiles.user_id' => $this->Auth->user('id')])->toArray();
        //debug($this->Auth->user('id')); return null;
        $session = $this->request->session();
        $session->write('papercodemapping', $testpapers);
        $session->write('cucetdata', $cucetdata);
        $session->write('scorecardUploaded', (count($file) > 0) ? true : false);
        $this->set('AuthId', $this->Auth->user('id'));
    }

    private function checkOrder($data) {
        $count = 0;
        $flag = true;
        for($count=0; $count < count($data); $count++ ) {
            switch($count) {
                case 0 :
                    break;
                case 1:
                    break;
                case 2:
                    $flag = (!empty($data[2]['selected']) && empty($data[1]['selected'])) ? false : true;
                    //debug(!empty($data[2]['selected']) && empty($data[1]['selected']));
                    break;
                default: 
                    break;
            }
        }
        return $flag;
    }
    
    public function viewresult()
    {
        return $this->redirect(['action' => 'add']);
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
        return $this->redirect(['action' => 'add']);
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
        return $this->redirect(['action' => 'add']);
        $this->request->allowMethod(['post', 'delete']);

        $preference = $this->Preferences->get($id);
        if ($this->Preferences->delete($preference)) {
            $this->Flash->success(__('The preference with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function isAuthorized($user = null) {
	//return parent::isAuthorized($user);
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

        
    }

}
