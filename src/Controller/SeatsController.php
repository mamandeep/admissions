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
        if(!isLockingSeatOpen()) {
            $this->Flash->error(__('The locking seat is closed.'));
        }
        return $this->redirect(['action' => 'lockseat']);
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
        if(!isLockingSeatOpen()) {
            $this->Flash->error(__('The locking seat is closed.'));
        }
        return $this->redirect(['action' => 'lockseat']);
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
        if(!isLockingSeatOpen()) {
            $this->Flash->error(__('The locking seat is closed.'));
        }
        return $this->redirect(['action' => 'lockseat']);
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
        if(!isLockingSeatOpen()) {
            $this->Flash->error(__('The locking seat is closed.'));
        }
        return $this->redirect(['action' => 'lockseat']);
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
        if(!isLockingSeatOpen()) {
            $this->Flash->error(__('The locking seat is closed.'));
        }
        return $this->redirect(['action' => 'lockseat']);
        $this->request->allowMethod(['post', 'delete']);

        $seat = $this->Seats->get($id);
        if ($this->Seats->delete($seat)) {
            $this->Flash->success(__('The seat with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function viewposition() {
        $flag = $this->isViewSeatOpen();
        if(!$flag) {
            $this->Flash->error(__('The viewposition of seat is closed at this time.'));
            $this->set('lockseatOpen', false);
        }
        else {
            $this->set('lockseatOpen', true);
        }
        //$this->request->allowMethod(['post', 'delete']);
        $lockseatsTable = TableRegistry::get('Candidates');
        $candidate = $lockseatsTable->find('all', ['fields' => 'id', 'conditions' => ['Candidates.user_id' => $this->Auth->user('id')]])->toArray();
        $candidate_id = '';
        if(count($candidate) === 1) {
            $candidate_id = $candidate[0]['id'];
        }
        else {
            $this->Flash->error(__('There is an error in you application. Please contact support.'));
            $this->set('lockseatOpen', false);
        }
        $rankingsTable = TableRegistry::get('Rankings');
        $seatOptions = $rankingsTable->find('all', ['conditions' => ['Rankings.candidate_id' => $candidate_id, 'Rankings.offered_seat is' => NULL ],
                'contain' => ['Programmes', 'Categories']]);
        $this->set('rankings', $seatOptions);
    }
    
    public function lockseat($AuthId = null) {
        $flag = $this->isLockingSeatOpen();
        if(!$flag) {
            $this->Flash->error(__('The locking of seat is closed at this time.'));
            $this->set('lockseatOpen', false);
        }
        else {
            $this->set('lockseatOpen', true);
        }
        //$this->request->allowMethod(['post', 'delete']);
        $lockseatsTable = TableRegistry::get('Candidates');
        $candidate = $lockseatsTable->find('all', ['fields' => 'id', 'conditions' => ['Candidates.user_id' => $this->Auth->user('id')]])->toArray();
        $candidate_id = '';
        if(count($candidate) === 1) {
            $candidate_id = $candidate[0]['id'];
        }
        else {
            $this->Flash->error(__('There is an error in you application. Please contact support.'));
            $this->set('lockseatOpen', false);
        }
        $lockseatsTable = TableRegistry::get('Lockseats');
        $rankingsTable = TableRegistry::get('Rankings');
        $seatOptions = $rankingsTable->find('all', ['conditions' => ['Rankings.candidate_id' => $candidate_id, 'Rankings.offered_seat is' => NULL],
                'contain' => ['Programmes', 'Categories']]);
        $rankingSelected = '';
        $lockedSeat = $lockseatsTable->find('all', ['conditions' => ['Lockseats.candidate_id' => $candidate_id]])->toArray();
        if(count($lockedSeat) > 1) {
            $this->Flash->error('An error has occured while locking seat. Please contact support.');
            return $this->redirect(['action' => 'lockseat']);
        }
        $lockedSeat = (count($lockedSeat) === 0) ? $lockseatsTable->newEntity() : $lockedSeat[0];
        
        if ($this->request->is(['patch', 'post', 'put']) && !empty($this->request->data()['selected_course']) && $flag === true) {
            //debug($this->request->data()); 
            $lockSeatData = [];
            $eligible_for_open = '';
            $category_pref = 0;
            $rankId = 0;
            $rankingObjectArr = $seatOptions->toArray();
            //debug($this->request->data());debug($rankingObjectArr); return null;
            if(!empty($this->request->data()['eligible_for_open'])) {
                foreach($this->request->data()['eligible_for_open'] as $key => $value){
                    //debug($key); debug($value);debug($this->request->data()['category_pref'][$key]);debug($rankingObjectArr[$key+$key]['final_category_id']);
                    if(intval($this->request->data()['category_pref'][$key]) === $rankingObjectArr[$key+$key]['final_category_id']) {
                        $str = $this->request->data()['selected_course'] . "_assoc";
                        //debug($rankingObjectArr[$key+$key]['id']);debug(intval($this->request->data()[$str]));
                        if($rankingObjectArr[$key+$key]['id'] === intval($this->request->data()[$str])) {
                            //$rankId = $this->request->data()[$str];
                            $eligible_for_open = $this->request->data()['eligible_for_open'][$key];
                            $category_pref = $this->request->data()['category_pref'][$key];
                            //debug("1");debug($eligible_for_open);
                        }
                    }
                    $rankId = $this->request->data()['selected_course'];
                    //debug(intval($this->request->data()['category_pref'][$key]));debug($rankingObjectArr[$key+$key+1]['final_category_id']);
                    if(intval($this->request->data()['category_pref'][$key]) === $rankingObjectArr[$key+$key+1]['final_category_id']) {
                        //debug($rankingObjectArr[$key+$key+1]['id']);debug(intval($this->request->data()['selected_course']));
                        if($rankingObjectArr[$key+$key+1]['id'] === intval($this->request->data()['selected_course'])) {
                            //$rankId = $this->request->data()['selected_course'];
                            $eligible_for_open = $this->request->data()['eligible_for_open'][$key];
                            $category_pref = $this->request->data()['category_pref'][$key];
                            //debug("2");debug($eligible_for_open);
                        }
                    }
                }
            }
            if(!empty(trim($eligible_for_open)) && strcmp($eligible_for_open, "yes") === 0 && intval($category_pref) === 1) {
                $rankingSelected = $rankingsTable->find('all', ['conditions' => ['Rankings.id' => $rankId]])->toArray();
                $rankingSelected = $rankingSelected[0];
                $lockSeatData['programme_id'] = $rankingSelected->programme_id;
                $lockSeatData['candidate_id'] = $rankingSelected->candidate_id;
                $lockSeatData['final_category_id'] = $rankingSelected->final_category_id;
                $lockSeatData['rank_id'] = $rankingSelected->id;
                $lockSeatData['user_id'] = $rankingSelected->user_id;
                $lockSeatData['eligible_for_open'] = $eligible_for_open;
                $lockSeatData['category_pref'] = $category_pref;
            }
            else {
                $rankingSelected = $rankingsTable->find('all', ['conditions' => ['Rankings.id' => $this->request->data()['selected_course']]])->toArray();
                $rankingSelected = $rankingSelected[0];
                $lockSeatData['programme_id'] = $rankingSelected->programme_id;
                $lockSeatData['candidate_id'] = $rankingSelected->candidate_id;
                $lockSeatData['final_category_id'] = $rankingSelected->final_category_id;
                $lockSeatData['rank_id'] = $rankingSelected->id;
                $lockSeatData['user_id'] = $rankingSelected->user_id;
                $lockSeatData['eligible_for_open'] = $eligible_for_open;
                $lockSeatData['category_pref'] = $category_pref;
            }
            $lockedSeat = $lockseatsTable->patchEntity($lockedSeat, $lockSeatData);
            //debug($lockedSeat); 
            if ($lockseatsTable->save($lockedSeat)) {
                $this->Flash->success('The seat has been locked.');
            }
            else {
                $this->Flash->error('The seat could not be locked. Please contact support.');
            }
        }
        else if($this->request->is(['patch', 'post', 'put']) && empty($this->request->data()['selected_course'])){
            $this->Flash->error('Please select the radio button under "Lock Seat" and then submit.');
        }
        else if($this->request->is(['patch', 'post', 'put']) && $flag === false) {
            $this->Flash->error(__('The locking of seats is closed at this time.'));
        }
        
        //debug($lockedSeat);
        //debug($seatOptions);
        $this->set('AuthId', $this->Auth->user('id'));
        $this->set('rankings', $seatOptions);
        //$this->set('lockedSeatRankId', $lockedSeat->rank_id);
        $this->set('lockedSeat', $lockedSeat);
        //debug($lockedSeat->rank_id);
    }
    
    public function summary($id = null) {
        $conn = ConnectionManager::get('default');
        $query_string = 'select s1.programme_id as p_id, p1.name as p_name, count(*) as Total_seats, SUM(CASE  WHEN category_id = 1 and candidate_id is not NULL THEN 1 ELSE 0 END) as Open_filled,
                        SUM(CASE  WHEN category_id = 3 and candidate_id is not NULL THEN 1 ELSE 0 END) as SC_filled,
                        SUM(CASE  WHEN category_id = 4 and candidate_id is not NULL THEN 1 ELSE 0 END) as ST_filled,
                        SUM(CASE  WHEN category_id = 5 and candidate_id is not NULL THEN 1 ELSE 0 END) as OBC_filled,
                        SUM(CASE  WHEN category_id = 1 and candidate_id is NULL THEN 1 ELSE 0 END) as Open_vacant,
                        SUM(CASE  WHEN category_id = 3 and candidate_id is NULL THEN 1 ELSE 0 END) as SC_vacant,
                        SUM(CASE  WHEN category_id = 4 and candidate_id is NULL THEN 1 ELSE 0 END) as ST_vacant,
                        SUM(CASE  WHEN category_id = 5 and candidate_id is NULL THEN 1 ELSE 0 END) as OBC_vacant
                        from seats s1
                        inner join programmes p1
                        on s1.programme_id = p1.id
                        group by s1.programme_id';
        $stmt = $conn->execute($query_string);
        $seatsSummary = $stmt->fetchAll('assoc');
        $totalSeats = '';
        $seatsFilled = '';
        $seatsVacant = '';
        foreach($seatsSummary as $programme) {
            $totalSeats  += $programme['Total_seats'];
            $seatsFilled += ($programme['Open_filled'] + $programme['SC_filled'] + $programme['ST_filled'] + $programme['OBC_filled'] );
            $seatsVacant += ($programme['Open_vacant'] + $programme['SC_vacant'] + $programme['ST_vacant'] + $programme['OBC_vacant'] );
        }
        $this->set('totalseats', $totalSeats);
        $this->set('seatsfilled', $seatsFilled);
        $this->set('seatsvacant', $seatsVacant);
        $this->set('summary', $seatsSummary);
    }

    
    public function summaryfees($id = null) {
        $conn = ConnectionManager::get('default');
        $query_string = 'select s1.programme_id as p_id, p1.name as p_name, count(*) as Total_seats, SUM(CASE  WHEN category_id = 1 and candidate_id is not NULL and fee_id is not NULL THEN 1 ELSE 0 END) as Open_filled,
                        SUM(CASE  WHEN category_id = 3 and candidate_id is not NULL and fee_id is not NULL THEN 1 ELSE 0 END) as SC_filled,
                        SUM(CASE  WHEN category_id = 4 and candidate_id is not NULL and fee_id is not NULL THEN 1 ELSE 0 END) as ST_filled,
                        SUM(CASE  WHEN category_id = 5 and candidate_id is not NULL and fee_id is not NULL THEN 1 ELSE 0 END) as OBC_filled,
                        SUM(CASE  WHEN category_id = 1 and fee_id is NULL THEN 1 ELSE 0 END) as Open_vacant,

                        SUM(CASE  WHEN category_id = 3 and fee_id is NULL THEN 1 ELSE 0 END) as SC_vacant,
                        SUM(CASE  WHEN category_id = 4 and fee_id is NULL THEN 1 ELSE 0 END) as ST_vacant,
                        SUM(CASE  WHEN category_id = 5 and fee_id is NULL THEN 1 ELSE 0 END) as OBC_vacant
                        from seats s1
                        inner join programmes p1
                        on s1.programme_id = p1.id
                        group by s1.programme_id';
        $stmt = $conn->execute($query_string);
        $seatsSummary = $stmt->fetchAll('assoc');
        $totalSeats = '';
        $seatsFilled = '';
        $seatsVacant = '';
        foreach($seatsSummary as $programme) {
            $totalSeats  += $programme['Total_seats'];
            $seatsFilled += ($programme['Open_filled'] + $programme['SC_filled'] + $programme['ST_filled'] + $programme['OBC_filled'] );
            $seatsVacant += ($programme['Open_vacant'] + $programme['SC_vacant'] + $programme['ST_vacant'] + $programme['OBC_vacant'] );
        }
        $this->set('totalseats', $totalSeats);
        $this->set('seatsfilled', $seatsFilled);
        $this->set('seatsvacant', $seatsVacant);
        $this->set('summary', $seatsSummary);
    }
    
    public function admissions($id = null) {
        $programmesTable = \Cake\ORM\TableRegistry::get('Programmes');
        $programmes = $programmesTable->find('list', ['fields'  =>  array('Programmes.id', 'Programmes.name')]);
        $programme = $programmesTable->newEntity();
        $programme_id = '';
        $conn = ConnectionManager::get('default');
        $query_string = '';
        $stmt = '';
        $summary = [];
        //debug($this->request->data());
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programme_id = !empty($this->request->data()['id'] && is_numeric($this->request->data()['id'])) ? intval($this->request->data()['id']) : 0;
            if($programme_id === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'admissions']);
            }
            $programme = $programmesTable->find('all')
                                         ->where(['Programmes.id' => $programme_id])->toArray();
            if(count($programme) === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'admissions']);
            }
            $programme = $programme[0];
            $query_string = 'select c1.cucet_roll_no as cucet_roll_no, c1.name as c_name, c2.type as c_category, c3.type as s_category, s1.fee_id as fee from 
                            seats s1
                            inner join candidates c1
                            on c1.id = s1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            inner join categories c3
                            on c3.id = s1.category_id
                            where s1.programme_id = ' . $programme_id . '
                            order by c_name asc;';
            $stmt = $conn->execute($query_string);
            $summary = $stmt->fetchAll('assoc');
            
        }
        $query_string = 'select s1.programme_id as p_id, p1.name as p_name, count(*) as Total_seats, SUM(CASE  WHEN candidate_id is not NULL THEN 1 ELSE 0 END) as Total_filled,
                        SUM(CASE  WHEN candidate_id is NULL THEN 1 ELSE 0 END) as Total_vacant
                        from seats s1
                        inner join programmes p1
                        on s1.programme_id = p1.id
                        group by s1.programme_id
                        ';
        $stmt = $conn->execute($query_string);
        $seatsSummary = $stmt->fetchAll('assoc');
        $totalSeats = '';
        $seatsFilled = '';
        $seatsVacant = '';
        foreach($seatsSummary as $programmeData) {
            $totalSeats  += $programmeData['Total_seats'];
            $seatsFilled += $programmeData['Total_filled'];
            $seatsVacant += $programmeData['Total_vacant'];
        }
        $this->set('totalseats', $totalSeats);
        $this->set('seatsfilled', $seatsFilled);
        $this->set('seatsvacant', $seatsVacant);
        $this->set('programme', $programme);
        $this->set('programmes', $programmes);
        $this->set('summary', $summary);
    }
    
    public function meritlist($id = null) {
        $conn = ConnectionManager::get('default');
        
        $programmesTable = \Cake\ORM\TableRegistry::get('Programmes');
        $programmes = $programmesTable->find('list', ['fields'  =>  array('Programmes.id', 'Programmes.name')]);
        $programme = $programmesTable->newEntity();
        $programme_id = 0;
        
        $query_string = '';
        $stmt = '';
        $summaryOpen = [];
        $summarySC = [];
        $summaryST = [];
        $summaryOBC = [];
        //debug($this->request->data());
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programme_id = (!empty($this->request->data()['id']) && is_numeric($this->request->data()['id'])) ? intval($this->request->data()['id']) : 0;
            if($programme_id === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'meritlist']);
            }
            $programme = $programmesTable->find('all')
                                         ->where(['Programmes.id' => $programme_id])->toArray();
            //debug($programme);
            if(count($programme) === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'meritlist']);
            }
            $programme = $programme[0];
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_B as marks_B, r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 1 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' and l1.eligible_for_open != \'no\' 
                            order by r1.rank';
            $stmt = $conn->execute($query_string);
            $summaryOpen = $stmt->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_B as marks_B, r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 3 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' 
                            order by r1.rank';
            $stmt = $conn->execute($query_string);
            $summarySC = $stmt->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_B as marks_B, r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 4 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' 
                            order by r1.rank';
            $stmt = $conn->execute($query_string);
            $summaryST = $stmt->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_B as marks_B, r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 5 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' 
                            order by r1.rank';
            $stmt = $conn->execute($query_string);
            $summaryOBC = $stmt->fetchAll('assoc');
            
        }
        
        $this->set('programme', $programme);
        $this->set('programmes', $programmes);
        $this->set('summaryOpen', $summaryOpen);
        $this->set('summarySC', $summarySC);
        $this->set('summaryST', $summaryST);
        $this->set('summaryOBC', $summaryOBC);
    }
    
    public function allocateseats($id = null) {
        $cocs = \Cake\ORM\TableRegistry::get('Cocs');
        $query = $cocs->find('list', ['condition' => ['user_id' => $this->Auth->user('id'),
                                                     'fields'  =>  array('Cocs.centre_id')]]);
        $id = array_keys($query->toArray())[0];
        
        $programmesTable = \Cake\ORM\TableRegistry::get('Programmes');
        $programmes = $programmesTable->find('list', ['fields'  =>  array('Programmes.id', 'Programmes.name')]);
        $programme = $programmesTable->newEntity();
        $programme_id = 0;
        $conn = ConnectionManager::get('default');
        $query_string = '';
        $stmt = '';
        $summaryOpen = [];
        $summarySC = [];
        $summaryST = [];
        $summaryOBC = [];
        $totalOpenSeats = '';
        $seatsOpenFilled = '';
        $seatsOpenVacant = '';
        $totalSCSeats = '';
        $seatsSCFilled = '';
        $seatsSCVacant = '';
        $totalSTSeats = '';
        $seatsSTFilled = '';
        $seatsSTVacant = '';
        $totalOBCSeats = '';
        $seatsOBCFilled = '';
        $seatsOBCVacant = '';
        
        $seatAllocationTable = TableRegistry::get('Seatallocations');
        $seatsTable = TableRegistry::get('Seats');
        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->data()); return null;
            $programme_id = (!empty($this->request->data()['id']) && is_numeric($this->request->data()['id'])) ? intval($this->request->data()['id']) : 0;
            if($programme_id === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'allocateseats']);
            }
            $query_string = 'select s1.programme_id as p_id, count(*) as Total_seats, 
                        SUM(CASE  WHEN candidate_id is not NULL THEN 1 ELSE 0 END) as Total_filled,
                        SUM(CASE  WHEN candidate_id is NULL THEN 1 ELSE 0 END) as Total_vacant,
                        SUM(CASE  WHEN category_id = 1 and candidate_id is NULL THEN 1 ELSE 0 END) as Open_vacant,
                        SUM(CASE  WHEN category_id = 3 and candidate_id is NULL THEN 1 ELSE 0 END) as SC_vacant,
                        SUM(CASE  WHEN category_id = 4 and candidate_id is NULL THEN 1 ELSE 0 END) as ST_vacant,
                        SUM(CASE  WHEN category_id = 5 and candidate_id is NULL THEN 1 ELSE 0 END) as OBC_vacant,
                        SUM(CASE  WHEN category_id = 1 and candidate_id is not NULL THEN 1 ELSE 0 END) as Open_filled,
                        SUM(CASE  WHEN category_id = 3 and candidate_id is not NULL THEN 1 ELSE 0 END) as SC_filled,
                        SUM(CASE  WHEN category_id = 4 and candidate_id is not NULL THEN 1 ELSE 0 END) as ST_filled,
                        SUM(CASE  WHEN category_id = 5 and candidate_id is not NULL THEN 1 ELSE 0 END) as OBC_filled
                        from seats s1
                        where s1.programme_id = ' . $programme_id ;
            $stmt = $conn->execute($query_string);
            $seatsSummary = $stmt->fetchAll('assoc');

            foreach($seatsSummary as $programmeData) {
                $totalOpenSeats  += $programmeData['Open_filled']+$programmeData['Open_vacant'];
                $seatsOpenFilled += $programmeData['Open_filled'];
                $seatsOpenVacant += $programmeData['Open_vacant'];
                $totalSCSeats  += $programmeData['SC_filled']+$programmeData['SC_vacant'];
                $seatsSCFilled += $programmeData['SC_filled'];
                $seatsSCVacant += $programmeData['SC_vacant'];
                $totalSTSeats  += $programmeData['ST_filled']+$programmeData['ST_vacant'];
                $seatsSTFilled += $programmeData['ST_filled'];
                $seatsSTVacant += $programmeData['ST_vacant'];
                $totalOBCSeats  += $programmeData['OBC_filled']+$programmeData['OBC_vacant'];
                $seatsOBCFilled += $programmeData['OBC_filled'];
                $seatsOBCVacant += $programmeData['OBC_vacant'];
            }
            
            if(!empty($this->request->data('Seatallocation'))) {
                //debug($this->request->data('Seatallocation')); return null;
                $conn->begin();
                
                $count = 0;
                $data = [];
                $saved = true;                
                
                $total_seats_in_prog = $seatsTable->find('all', ['conditions' => ['programme_id' => $programme_id]])->toArray();
                foreach($this->request->data('Seatallocation') as $seat) {
                    if(intval($seat['idcheck']) === 1) {
                        $seat_id = 0;
                        $candidate_id = 0;
                        foreach ($total_seats_in_prog as $seat_fixed) {
                            //debug($seat_fixed); debug($seat_fixed['candidate_id']); debug($seat_fixed['category_id'] === intval($seat['seat_category_id']));
                            if(empty($seat_fixed['candidate_id']) && $seat_fixed['category_id'] === intval($seat['seat_category_id'])) {
                                $seat_fixed['candidate_id'] = $seat['candidate_id'];
                                $seat_id = $seat_fixed['id'];
                                $candidate_id = $seat['candidate_id'];
                                break;
                            }
                        }
                        $seat1 = $seatAllocationTable->newEntity(['candidate_id' => $candidate_id, 'seat_id' => $seat_id, 'user_id' => $this->Auth->user('id')]);
                        if(!$seatAllocationTable->save($seat1)) {
                            $saved = false;
                        }
                    }
                }
                //return null;
                if(!$seatsTable->saveMany($total_seats_in_prog)) {
                    $saved = false;
                }
                
                if ($saved) {
                    $conn->commit();
                } else {
                    $conn->rollback();
                }
                
            }
            $programme_id = (!empty($this->request->data()['id']) && is_numeric($this->request->data()['id'])) ? intval($this->request->data()['id']) : 0;
            if($programme_id === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'allocateseats']);
            }
            $programme = $programmesTable->find('all')
                                         ->where(['Programmes.id' => $programme_id])->toArray();
            //debug($programme);
            if(count($programme) === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'allocateseats']);
            }
            $programme = $programme[0];
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 1 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' and l1.eligible_for_open != \'no\' and l1.candidate_id not in (select candidate_id from seats where candidate_id is not NULL and programme_id = ' . $programme_id . ')
                            order by r1.rank asc limit 50';
            $stmt = $conn->execute($query_string);
            $summaryOpen = $stmt ->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 3 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' and l1.candidate_id not in (select candidate_id from seats where candidate_id is not NULL and programme_id = ' . $programme_id . ')
                            order by r1.rank asc limit 25';
            $stmt = $conn->execute($query_string);
            $summarySC = $stmt ->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 4 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' and l1.candidate_id not in (select candidate_id from seats where candidate_id is not NULL and programme_id = ' . $programme_id . ')
                            order by r1.rank asc limit 25';
            $stmt = $conn->execute($query_string);
            $summaryST = $stmt ->fetchAll('assoc');
            
            $query_string = 'select r1.rank as merit, c1.cucet_roll_no as rollno, c1.name as c_name, c2.type as c_category, l1.category_pref as c_category_pref, l1.candidate_id as candidate_id, l1.programme_id as programme_id,
                            r1.marks_total as total_marks
                            from lockseats l1
                            inner join rankings r1
                            on l1.candidate_id = r1.candidate_id and r1.final_category_id = 5 and r1.programme_id = l1.programme_id
                            inner join candidates c1
                            on c1.id = l1.candidate_id
                            inner join categories c2
                            on c2.id = c1.category_id
                            where l1.programme_id = ' . $programme_id . ' and l1.candidate_id not in (select candidate_id from seats where candidate_id is not NULL and programme_id = ' . $programme_id . ')
                            order by r1.rank asc limit 40';
            $stmt = $conn->execute($query_string);
            $summaryOBC = $stmt ->fetchAll('assoc');
            
            
        }
        
        
        $this->set('totalOpenSeats', $totalOpenSeats);
        $this->set('seatsOpenFilled', $seatsOpenFilled);
        $this->set('seatsOpenVacant', $seatsOpenVacant);
        $this->set('totalSCSeats', $totalSCSeats);
        $this->set('seatsSCFilled', $seatsSCFilled);
        $this->set('seatsSCVacant', $seatsSCVacant);
        $this->set('totalSTSeats', $totalSTSeats);
        $this->set('seatsSTFilled', $seatsSTFilled);
        $this->set('seatsSTVacant', $seatsSTVacant);
        $this->set('totalOBCSeats', $totalOBCSeats);
        $this->set('seatsOBCFilled', $seatsOBCFilled);
        $this->set('seatsOBCVacant', $seatsOBCVacant);
        
        $this->set('programme', $programme);
        $this->set('programmes', $programmes);
        $this->set('summaryOpen', $summaryOpen);
        $this->set('summarySC', $summarySC);
        $this->set('summaryST', $summaryST);
        $this->set('summaryOBC', $summaryOBC);
    }
    
    public function printseats() {
	$conn = ConnectionManager::get('default');
	$query_string = 'select c1.name as c_name, p2.name as p_name, r1.marks_total,  				c3.type as c_category, c4.type as s_category, u1.username from seats s2
			inner join candidates c1
			on s2.candidate_id = c1.id and s2.fee_id is NULL
		        inner join users u1
            		on u1.id = c1.user_id
			inner join rankings r1
			on r1.candidate_id = c1.id and r1.programme_id = s2.programme_id and r1.final_category_id = s2.category_id
			inner join programmes p2
			on p2.id = s2.programme_id
			left join categories c3
			on c3.id = c1.category_id
			left join categories c4
			on c4.id = s2.category_id
			order by s2.programme_id, s2.category_id, r1.rank;';
       $stmt = $conn->execute($query_string);
       $seatsallocated = $stmt ->fetchAll('assoc');
        
        $this->set('seatallocations', $seatsallocated);
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
	return parent::isAuthorized($user);
        if(parent::isAuthorized($user)) {
		return true;
	}
        
        /*if ($this->request->getParam('action') === 'index') {
            return true;
        }*/
        
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'lockseat'
                || $this->request->getParam('action') === 'submitfee' || $this->request->getParam('action') === 'seatalloted'
                || $this->request->getParam('action') === 'viewposition')) {
            return true;
        }
        // All users with role as 'exam' can add seats
        if (isset($user['role']) && $user['role'] === 'exam' && ($this->request->getParam('action') === 'add' 
            || $this->request->getParam('action') === 'centre' || $this->request->getParam('action') === 'allocateseats'
            || $this->request->getParam('action') === 'printseats' || $this->request->getParam('action') === 'summary'
            || $this->request->getParam('action') === 'admissions' || $this->request->getParam('action') === 'meritlist')) {
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
