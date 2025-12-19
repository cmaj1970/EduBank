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
                'controller' => 'Accounts',
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

        # CSRF-Protection aktiviert (Security-Hardening)
        # Nur CSRF-Token-Validierung, keine SSL-Erzwingung oder Formular-Tampering-Checks
        $this->loadComponent('Security', [
            'blackHoleCallback' => 'blackhole',
            'requireSecure' => false,  # Keine SSL-Erzwingung (für lokale Entwicklung)
            'validatePost' => false    # Kein Formular-Tampering-Check (würde bestehende Forms brechen)
        ]);

        // Prior to 3.5 use I18n::locale()
        I18n::setLocale('de_DE');
        $this->school = null;
        if($this->request->getSession()->read('Auth') && substr($this->request->getSession()->read('Auth')['User']['username'], 0, 6) == 'admin-') {
            $this->loadModel('Schools');
            $schoolshortname = str_replace('admin-', '', $this->request->getSession()->read('Auth')['User']['username']);
            $this->school = $this->Schools->find('all', array('conditions' => array('kurzname' => $schoolshortname)))->first();
            $this->set('loggedinschool', $this->school);
        } else {

        }
    }
    public function beforeFilter(Event $event)
        {
            #$this->Auth->allow(['index', 'view', 'display']);
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
    * Blackhole-Callback für Security-Component
    * Wird aufgerufen wenn CSRF-Token fehlt/ungültig ist
    */
   public function blackhole($type)
   {
       # Log für Debugging
       $this->log('Security blackhole: ' . $type, 'error');

       # Benutzerfreundliche Fehlermeldung
       if ($type === 'csrf') {
           $this->Flash->error(__('Sicherheitswarnung: Ihre Sitzung ist abgelaufen oder die Anfrage war ungültig. Bitte versuchen Sie es erneut.'));
       } else {
           $this->Flash->error(__('Sicherheitswarnung: Ungültige Anfrage.'));
       }

       # Redirect zur Login-Seite
       return $this->redirect(['controller' => 'Users', 'action' => 'login']);
   }
}
