<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class PaymentsController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function index() {
        return $this->redirect(['action' => 'submitfee']);
        // List of all payments
        $payments = $this->Payments->find('all');
        $this->set('payments', $payments);
    }

    public function view($id) {
        return $this->redirect(['action' => 'submitfee']);
        // View a particular payment
        $payment = $this->Payments->get($id);
        $this->set(compact('paymentat'));
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
        return $this->redirect(['action' => 'submitfee']);
        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $payment = $this->Payments->patchEntity($payment, $this->request->getData());
            // Added this line
            $seat->user_id = $this->Auth->user('id');
            // You could also do the following
            //$newData = ['user_id' => $this->Auth->user('id')];
            //$article = $this->Articles->patchEntity($article, $newData);
            if ($this->Payments->save($payment)) {
                $this->Flash->success(__('Your payment has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your payment.'));
        }
        $this->set('payment', $payment);
    }
    
    public function submitfee() {
        $flag = $this->isSubmitFeeOpen();
        if($flag) {
            $this->set('submitFeeOpen', true);
        }
        else {
            $this->set('submitFeeOpen', false);
        }
        $candidatesTable = TableRegistry::get('Candidates');
        $candidate = $candidatesTable->find('list')->where(['Candidates.user_id' => $this->Auth->user('id')]);
        
        //debug($candidate->toArray()); 
        $candidateId = (count($candidate->toArray()) > 0) ? array_keys($candidate->toArray())[0] : 0;
        $conn = ConnectionManager::get('default');
        $query_string = 'select p1.id as p_id, p1.name as programme, c1.type as category
                        from seats s2
                        inner join categories c1
                        on c1.id = s2.category_id
                        inner join programmes p1
                        on s2.programme_id = p1.id
                        where s2.candidate_id = ' . $candidateId . ' and s2.fee_id is not NULL;';
        $stmt = $conn->execute($query_string);
        $seatConfirmed = $stmt->fetchAll('assoc');

        $query_string = 'select p1.id as p_id, p1.name as programme, c1.type as category
                        from seats s2
                        inner join categories c1
                        on c1.id = s2.category_id
                        inner join programmes p1
                        on s2.programme_id = p1.id
                        where s2.candidate_id = ' . $candidateId . ' and s2.fee_id is NULL;';
        $stmt = $conn->execute($query_string);
        $seatAlloted = $stmt->fetchAll('assoc');
        if($this->request->is(['post', 'put']) && $flag === true) {
            $session = $this->request->session();
            $token = $session->read('feetoken');
            //debug($this->request->data()); return null;
            if($token === $this->request->data()['tokenid']) {
                $this->redirect(['action' => 'pay']);
            }
            // Flash error
        }
        else if($this->request->is(['post', 'put']) && $flag === false) {
            $this->Flash->error(__('Submission of fee is closed at this time.'));
        }
        $token = uniqid();
        $session = $this->request->session();
        $session->write('feetoken', $token);
        
        if(count($seatAlloted) >= 1) {
            $seatAlloted = $seatAlloted[0];
        }
        else if(count($seatAlloted) === 0) {
            $seatAlloted = [];
        }
        $this->set('seatAlloted', $seatAlloted);
	$this->set('seatConfirmed', $seatConfirmed);
        $query_string = 'select f1.response_code as response_code, p2.name as p_name, 
			c2.type as s_category
                        from fees f1
                        inner join seats s2
                        on s2.candidate_id = f1.candidate_id and s2.candidate_id = ' . $candidateId . '
			inner join programmes p2
			on s2.programme_id = p2.id
			inner join categories c2
			on c2.id = s2.category_id;';
        
        $stmt = $conn->execute($query_string);
        $feePaid = $stmt->fetchAll('assoc');
        //debug($feePaid[0]['response_code']);
        $session->write('programme_id_for_fee', (!empty($seatAlloted['p_id']) ? $seatAlloted['p_id'] : 0));
        $this->set('token', $token);
        $this->set('feePaid', ((count($feePaid) > 0) ? ((intval($feePaid[0]['response_code']) === 0) ? true : false) : false));
	$this->set('feesPaidFor', $feePaid);
    }
    
    public function pay() {
        
        $session = $this->request->session();
        $programe_id = intval($session->read('programme_id_for_fee'));
        //debug($programe_id);
        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->find('all', ['conditions' => ['Users.id' => $this->Auth->user('id')]])->toArray();
        $programmesTable = TableRegistry::get('Programmes');
        $programme = $programmesTable->find('all', ['conditions' => [ 'Programmes.id' => $programe_id]])->toArray();
        $fee = 0;
        $applicantId = '';
        if(count($programme) === 1 && count($user) === 1) {
            $fee = $programme['0']['fee'];
            $applicantId = $user[0]['username'];
        }
        else {
            $this->Flash->error(__('The programme is not found in the list. Please contact support.'));
            return $this->redirect(['action' => 'submitfee']);
        }
        
        $this->set('app_fee', $fee);
        
        $session->write('payment_amount', $fee);
        //debug($fee); debug($applicantId);
        $this->set('applicantId', $applicantId);
    }
    
    public function post() {
		//print_r($this->request->data);
        $session = $this->request->session();
        $HASHING_METHOD = 'sha512'; // md5,sha1
        $ACTION_URL = "https://secure.ebs.in/pg/ma/payment/request/";

        $this->set('ACTION_URL',$ACTION_URL);		
        if(isset($_POST['secretkey']))
                $_SESSION['SECRET_KEY'] = $_POST['secretkey'];
        else
                $_SESSION['SECRET_KEY'] = ''; //set your secretkey here

        $hashData = $_SESSION['SECRET_KEY'];

        unset($_POST['secretkey']);
        unset($_POST['submitted']);

        ksort($_POST);
        foreach ($_POST as $key => $value) {
                if (strlen($value) > 0) {
                        if($key == "amount") {
                                $hashData .= '|'. $session->read('payment_amount');
                        }
                        else {
                                $hashData .= '|'.$value;
                        }
                }
        }
        if (strlen($hashData) > 0) {
                $secureHash = strtoupper(hash($HASHING_METHOD, $hashData));
                $this->set('secureHash', $secureHash);
        }
    }
    
    public function returnpg() {
        $HASHING_METHOD = 'sha512'; // md5,sha1

        // This response.php used to receive and validate response.
        if(!isset($_SESSION['SECRET_KEY']) || empty($_SESSION['SECRET_KEY']))
        $_SESSION['SECRET_KEY'] = ''; //set your secretkey here

        $hashData = $_SESSION['SECRET_KEY'];
        ksort($_POST);
        foreach ($_POST as $key => $value){
                if (strlen($value) > 0 && $key != 'SecureHash') {
                        $hashData .= '|'.$value;
                }
        }
        if (strlen($hashData) > 0) {
                $secureHash = strtoupper(hash($HASHING_METHOD , $hashData));

                if(!empty($_POST['SecureHash']) && $secureHash == $_POST['SecureHash']){

                    if($_POST['ResponseCode'] == 0 && intval($this->Auth->user('id')) > 0) {
                            // update response and the order's payment status as SUCCESS in to database		    
			    $usersTable = TableRegistry::get('Users');
			    $user = $usersTable->find('all', ['conditions' => ['Users.id' => $this->Auth->user('id')]])->toArray();
			    $user = $user[0];
                            $candidatesTable = TableRegistry::get('Candidates');
                            $candidate = $candidatesTable->find('list')->where(['Candidates.user_id' => $this->Auth->user('id')]);

                            //debug($candidate->toArray()); 
                            $candidateId = array_keys($candidate->toArray())[0];
                            
                            $seatsTable = TableRegistry::get('Seats');
                            $feesTable = TableRegistry::get('Fees');
                            $fee = '';
                            $seat = '';
                            $conn = ConnectionManager::get('default');
                            $conn->begin();
                            $saved = true;
                            $query_string = 'select s1.id as seat_id from seats s1
                                            where s1.candidate_id = ' . $candidateId . ' and s1.fee_id is NULL;';
                            $stmt = $conn->execute($query_string);
                            $seatAllocated = $stmt->fetchAll('assoc');
                            if(count($seatAllocated) >= 1) {
                                $seat = $seatsTable->find('all', ['conditions' => ['Seats.candidate_id' => $candidateId, 'Seats.fee_id is ' => NULL]])->toArray()[0];
                                $fee = $feesTable->newEntity(['response_code' => $_POST['ResponseCode'],
                                                        'payment_date_created' => $_POST['DateCreated'],
                                                        'payment_id' => $_POST['PaymentID'],
                                                        'payment_amount' => $_POST['Amount'],
                                                        'payment_transaction_id' => $_POST['TransactionID']]);
                                /*$fee = $feesTable->newEntity(['response_code' => '0',
                                                        'payment_date_created' => '2016-08-16 11:16:19',
                                                        'payment_id' => '58377379',
                                                        'payment_amount' => '9000',
                                                        'payment_transaction_id' => '159653335']);*/
                                $fee->user_id = $this->Auth->user('id');
                                $fee->candidate_id = $candidateId;
                            }
                            else {
                                $this->Flash->error(__('The seat allocated details are mismatched. Please contact support.'));
                                return $this->redirect(['action' => 'submitfee']);
                            }
                            $result = '';
                            if(!($result = $feesTable->save($fee))) {
                                $saved = false;
                            }
                            if($saved) {
                                $seat->fee_id = $result->id;
                            }
                            if(!($result = $seatsTable->save($seat))) {
                                $saved = false;
                            }
		            else {
				if($this->is_connected()) {
					$response = $this->smsSend($user['mobile'], 'Dear '.$user['name'].'! Your fee has been successfully received. Payment ID:'. $_POST['PaymentID']);
				 }
			    }
                            if ($saved) {
                                $conn->commit();
                                $_POST['paymentStatus'] = 'SUCCESS';
                                $_SESSION['paymentResponse'][$_POST['PaymentID']] = $_POST;
                                $this->set('paymentStatus', $_POST['ResponseCode']);
                                $this->set('paymentStatusStr', 'SUCCESS');
                                $this->set('paymentID', $_POST['PaymentID']);
                                $this->set('transID', $_POST['TransactionID']);
                                $this->set('tras_amount', $_POST['Amount']);
                                $this->set('tras_date', $_POST['DateCreated']);
                            } else {
                                $conn->rollback();
                                $this->Flash->error(__('There was an error in saving the fee details. Please contact support.'));
                            }
                            //$this->redirect(array('controller' => 'form', 'action' => 'appliedposts'));
                            //for demo purpose, its stored in session
                            

                    } else {
                            // update response and the order's payment status as FAILED in to database
                            $this->set('error_mesg', $_POST['Error']);
                            //for demo purpose, its stored in session
                            $_POST['paymentStatus'] = 'FAILED';
                            $_SESSION['paymentResponse'][$_POST['PaymentID']] = $_POST;
                    }
                    // Redirect to confirm page with reference.
                    $confirmData = array();
                    $confirmData['PaymentID'] = $_POST['PaymentID'];
                    $confirmData['Status'] = $_POST['paymentStatus'];
                    $confirmData['Amount'] = $_POST['Amount'];

                    $hashData = $_SESSION['SECRET_KEY'];

                    ksort($confirmData);
                    foreach ($confirmData as $key => $value){
                            if (strlen($value) > 0) {
                                    $hashData .= '|'.$value;
                            }
                    }
                    if (strlen($hashData) > 0) {
                            $secureHash = strtoupper(hash($HASHING_METHOD , $hashData));
                    }
                } else {
                        //echo '<h1>Error!</h1>';
                        //echo '<p>Hash validation failed</p>';
                        $this->set('error_mesg', "Hash validation failed");
                }
        } else {
                //echo '<h1>Error!</h1>';
                //echo '<p>Invalid response</p>';
                $this->set('error_mesg', "Invalid response.");
        }
    }
    
    public function cancelseat() {
	$conn = ConnectionManager::get('default');
	$candidatesTable = TableRegistry::get('Candidates');
        $candidate = $candidatesTable->find('list')->where(['Candidates.user_id' => $this->Auth->user('id')]);
        
        $candidateId = array_keys($candidate->toArray())[0];

	$query_string = 'select s1.id as seat_id, s1.programme_id as p_id, p1.name as p_name  
			from seats s1 
			inner join programmes p1
			on s1.programme_id = p1.id 
                        where s1.candidate_id = ' . $candidateId . ' and s1.fee_id is not NULL;';
        $stmt = $conn->execute($query_string);
        $programmmes_alloted = $stmt->fetchAll('assoc');
	$programmesTable = \Cake\ORM\TableRegistry::get('Programmes');
	$programme = $programmesTable->newEntity();
        $programme_id = 0;
        $userData = [];
        
        $saved = true;
	$cancelseatsTable = TableRegistry::get('Cancelseats');
        $cancelseats = $cancelseatsTable->find('all')->where(['Cancelseats.user_id' => $this->Auth->user('id')])->toArray();
        $seatsTable = TableRegistry::get('Seats');
	$cancelseat = $cancelseatsTable->newEntity();
	$this->set('seatCancelledAlready', false);
        if ($this->request->is(['patch', 'post', 'put'])) {
		debug($this->request->data());
	    $programme_id = (!empty($this->request->data()['pid']) && is_numeric($this->request->data()['pid'])) ? intval($this->request->data()['pid']) : 0;
            if($programme_id === 0) {
                $this->Flash->error(__('The programme selected is not a valid programme.'));
                return $this->redirect(['action' => 'cancelseat']);
            }
	    $programme = $programmesTable->find('all')
                                         ->where(['Programmes.id' => $programme_id])->toArray();
	    //debug($programme);
	    if(count($programme) === 0) {
		$this->Flash->error(__('The programme selected is not a valid programme.'));
		return $this->redirect(['action' => 'cancelseat']);
	    }
	    $programme = $programme[0];
	    $seatCancelledAlready = false;
	    foreach($cancelseats as $cseat) {
                $seat = $seatsTable->find('all', ['conditions' => ['Seats.id' => $cseat['seat_id']]])->toArray()[0];
		if($seat['programme_id'] === $programme_id) {
		      $seatCancelledAlready = true;
		      $this->set('seatCancelledAlready', true);
		      $cancelseat = $cseat;
		}
	    }
	    if($seatCancelledAlready === false && !empty($this->request->data('ac_number'))) {
		    $conn->begin();
		    //debug($this->request->data());
		    $cancelseat = $cancelseatsTable->patchEntity($cancelseat, $this->request->data());
		    $cancelseat->user_id = $this->Auth->user('id');
		    $cancelseat->candidate_id = $candidateId;
		    //debug($cancelseat);
		    if(!$cancelseatsTable->save($cancelseat)) {
			$saved = false;
		    }
		    
		    if ($saved) {
			$conn->commit();
			$this->Flash->success(__('Your cancellation request has been successfully received.'));
		    } else {
			$conn->rollback();
			$this->Flash->error(__('There was an error in cancellation request of the seat. Please correct the field below or contact support.'));
            	   }
	    }
	    else if($seatCancelledAlready === true && !empty($this->request->data('ac_number'))) {
		$this->Flash->error(__('The request for seat cancellation has already been submitted for this programme.'));
	    }
	    $query_string = 'select u1.username as applicant_id, c1.name as c_name, c1.f_name as f_name,
                        u1.email as email, u1.mobile as mobile, p1.name as p_name, c2.type as category,
                        f1.payment_id as bank_payment_id, f1.payment_date_created as fee_submit_date,
                        f1.payment_amount as amount, f1.id as fee_id, s1.id as seat_id
                        from candidates c1
                        inner join users u1
                        on c1.user_id = u1.id and c1.id = ' . $candidateId . '
                        inner join seats s1
                        on s1.candidate_id = c1.id and s1.fee_id is not NULL and s1.programme_id = ' . $programme_id . ' 
                        inner join fees f1
                        on f1.candidate_id = c1.id and f1.id = s1.fee_id
                        inner join programmes p1
                        on p1.id = s1.programme_id
                        inner join categories c2
                        on c2.id = s1.category_id
                        where u1.id = ' . $this->Auth->user('id') . '
                        group by c1.id, f1.payment_id';
		$stmt = $conn->execute($query_string);
		$userData = $stmt->fetchAll('assoc');
            
            
        }
        
	
        
        if(count($userData) === 1) {
            $userData = $userData[0];	    
	    $this->set('cancelseat', $cancelseat);
	    $this->set('userData', $userData);
        }
	$this->set('programme', $programme);
	$this->set('programmes_alloted', $programmmes_alloted);
    }
    
    public function isAuthorized($user = null) {
	//return parent::isAuthorized($user);
        if(parent::isAuthorized($user)) {
		return true;
	}
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'index' || $this->request->getParam('action') === 'submitfee'
                || $this->request->getParam('action') === 'pay' || $this->request->getParam('action') === 'post'
                || $this->request->getParam('action') === 'returnpg' || $this->request->getParam('action') === 'cancelseat')) {
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
    }
}
