<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Cache\Cache;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        Cache::disable();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
		
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'username', 'password' => 'password'],
                    'passwordHasher' => array(
                        'className' => 'Default'
                    )
                ]
            ],
            'storage' => 'Session',
            'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'seats',
                'action' => 'index'
            ],
            /*'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'*/
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'login',
                'home'
            ],
            'unauthorizedRedirect' => [
                'controller' => 'candidates',
                'action' => 'index',
                'prefix' => false
            ],
            'authError' => 'You must be authorized to view this page.',
            'loginError' => 'Invalid Username or Password entered, please try again.'
        ]);
        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        $this->loadComponent('Auth');
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
        if ($this->Auth->user()) {
            $this->viewBuilder()->theme('AdminLTE');
            $this->set('theme', Configure::read('Theme'));
        }
        $this->set('user', $this->Auth->user());
    }
	
	public function beforeFilter(Event $event)
        {
            $this->Auth->allow(['logout']);
        }
    
        public function isAuthorized($user)
        {
            // Admin can access every action
            if (isset($user['role']) && $user['role'] === 'admin') {
                return true;
            }

            // Default deny
            return false;
        }
}
