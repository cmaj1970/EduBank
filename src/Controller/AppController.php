<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\I18n\Date;
use Cake\I18n\Number;
use Cake\Utility\Inflector;



/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
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
		$this->loadComponent('Flash');
        $this->loadComponent('Auth', [
        	'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'Users',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login',
                'home'
            ],
            'authError' => "Bitte melden Sie sich an."
        ]);
        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
		$this->set('authuser', $this->Auth->user());

        // CSRF protection enabled (security hardening)
        // Only CSRF token validation, no SSL enforcement or form tampering checks
        $this->loadComponent('Security', [
            'blackHoleCallback' => 'blackhole',
            'requireSecure' => false,  // No SSL enforcement (for local development)
            'validatePost' => false    // No form tampering check (would break existing forms)
        ]);

        // Prior to 3.5 use I18n::locale()
        I18n::setLocale('de_DE');
        $this->school = null;
        if($this->request->getSession()->read('Auth') && substr($this->request->getSession()->read('Auth')['User']['username'], 0, 6) == 'admin-') {
            $this->loadModel('Schools');
            $schoolshortname = str_replace('admin-', '', $this->request->getSession()->read('Auth')['User']['username']);
            $this->school = $this->Schools->find('all', array('conditions' => array('kurzname' => $schoolshortname)))->first();
            $this->set('loggedinschool', $this->school);

            // Check if school is pending verification
            if ($this->school && $this->school->status === 'pending') {
                $this->set('schoolPendingVerification', true);
            }
        }
    }
    public function beforeFilter(Event $event)
    {
        // Redirect pending schools to verification page
        if (isset($this->school) && $this->school && $this->school->status === 'pending') {
            // Allow access to logout and the pending page itself
            $allowedActions = ['logout', 'pendingVerification', 'resendVerification'];
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');

            if (!($controller === 'Schools' && in_array($action, $allowedActions)) &&
                !($controller === 'Users' && $action === 'logout')) {
                return $this->redirect(['controller' => 'Schools', 'action' => 'pendingVerification']);
            }
        }
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

   /**
    * Blackhole callback for Security component
    * Called when CSRF token is missing or invalid
    *
    * @param string $type The type of security violation
    * @return \Cake\Http\Response|null
    */
   public function blackhole($type)
   {
       // Log for debugging
       $this->log('Security blackhole: ' . $type, 'error');

       // User-friendly error message
       if ($type === 'csrf') {
           $this->Flash->error(__('Sicherheitswarnung: Ihre Sitzung ist abgelaufen oder die Anfrage war ungültig. Bitte versuchen Sie es erneut.'));
       } else {
           $this->Flash->error(__('Sicherheitswarnung: Ungültige Anfrage.'));
       }

       // Redirect to login page
       return $this->redirect(['controller' => 'Users', 'action' => 'login']);
   }
}
