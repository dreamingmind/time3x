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

use Cake\Controller\Component\FlashComponent;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\Session;
use Cake\ORM\TableRegistry;

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
     * @var FlashComponent
     */
    protected $Flash;

    /**
     * @var Session
     */
    protected $Session;

    /**
     * The User data from the session
     *
     * keys: entity, id, name, username
     *
     * @var null|array
     */
    protected $_userSession;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @throws \Exception
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->Session = $this->request->getSession();
//        $this->Session->delete('User');
        $this->readUser();

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }


    public function userSession()
    {
        return $this->_userSession;
    }

    protected function readUser() {
        $UserSessionData = $this->Session->read('User');
        if (!is_null($UserSessionData)) {
            $UserSessionData['entity'] = unserialize($UserSessionData['entity']);
        }
        $this->_userSession = $UserSessionData;
    }

    protected function writeUser($entity)
    {
        $this->Session->write('User.entity', serialize($entity));
        $this->Session->write('User.id', $entity->id);
        $this->Session->write('User.name', $entity->name);
        $this->Session->write('User.username', $entity->username);
    }

}
