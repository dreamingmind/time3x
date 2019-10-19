<?php
namespace App\Lib;

use Cake\ORM\TableRegistry;

/**
 * ReportComponent
 *
 * Provide report analyses for time records
 *
 * @author dondrake
 */
class Summaries {

	public $components = array();

	/**
	 * The current time record of interest
	 *
	 * This is a transient value. Be sure you set it when you need it.
	 * Field names are first level indexes.
	 *
	 * @var array
	 */
	private $time;

	/**
	 * Total time for users given current found Time records
	 *
	 * User A
	 *	Time : total
	 *	Projects
	 *		project a
	 *			time : total
	 *			task a : total
	 *			task b : total
	 *		project b
	 *			time : total
	 * User B
	 *	.
	 *	.
	 *	.
	 *
	 * @var array
	 */
	private $userCumm;

	/**
	 * Project time breakdown given current found Time records
	 *
	 * project A
	 *	Time : total
	 *	Users
	 *		user a
	 *			time : total
	 *		user b
	 *			time : total
	 *	Tasks
	 *		task a
	 *			time : total
	 *			user a : time total
	 *			user b : time total
	 * project B
	 *	.
	 *	.
	 *	.
	 *
	 * @var array
	 */
	private $projectCumm;

	/**
	 * Lookup list to convert project IDs to names
	 *
	 * @var array
	 */
	private $projects;

	/**
	 * Lookup list to convert user IDs to names
	 *
	 * @var array
	 */
	private $users;

	/**
	 * Lookup list to convert task IDs to names
	 *
	 * @var array
	 */
	private $tasks;

	public function initialize(Controller $controller) {

	}

	public function startup(Controller $controller) {

	}

	public function beforeRender(Controller $controller) {

	}

	public function shutDown(Controller $controller) {

	}

	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {

	}

	/**
	 * Summarize the times for this set of time records
	 *
	 * Numerically indexed set of time records.
	 * 0 => array(
	 *	field => value
	 *	field => value
	 * 1 => array(
	 *	.
	 *	.
	 *	.
	 *
	 * sets userCumm and projectCumm
	 *
	 * @param array $timeEntries
	 */
	public function summarizeUsers($timeEntries) {
		// build id=>name lookup properties;
		$this->initProperties();

		foreach ($timeEntries as $time) {
			$this->time = $time;
			$duration = $this->duration($time);
            $this->userUserCumm($duration);
            $this->userProjectCumm($duration);
            $this->userProjectTaskCumm($duration);
		}
		return $this->userCumm;
	}

	/**
	 * Summarize the times for this set of time records
	 *
	 * Numerically indexed set of time records.
	 * 0 => array(
	 *	field => value
	 *	field => value
	 * 1 => array(
	 *	.
	 *	.
	 *	.
	 *
	 * sets userCumm and projectCumm
	 *
	 * @param array $timeEntries
	 */
	public function summarizeProjects($timeEntries) {
		// build id=>name lookup properties;
		$this->initProperties();

		foreach ($timeEntries as $time) {
			$this->time = $time;
			$duration = $this->duration($time);
			$this->projectUserCumm($duration);
			$this->projectProjectCumm($duration);
			$this->projectProjectUserCumm($duration);
			$this->projectTaskCumm($duration);
			$this->projectTaskUserCumm($duration);
		}
	}

	/**
	 * Return the current User Time summary
	 *
	 * @return array
	 */
	public function userTime() {
		if (is_array($this->userCumm)) {
			return $this->userCumm;
		} else {
			return array();
		}
	}

	/**
	 * Return the current Project Time summary
	 *
	 * @return array
	 */
	public function projectTime() {
		if (is_array($this->projectCumm)) {
			return $this->projectCumm;
		} else {
			return array();
		}
	}

	/**
	 * Make the ID -> name lookup lists
	 *
	 * These are used to make meaningful indexes in the time summary arrays
	 */
	private function initProperties(){
        $projects = TableRegistry::getTableLocator()->get('Projects');
        $users = TableRegistry::getTableLocator()->get('Users');
        $tasks = TableRegistry::getTableLocator()->get('Tasks');
        $this->projects = $projects->find('list');
		$this->users = $users->find('list');
		$this->tasks = $tasks->find('list');
		debug($projects);
		debug($users);
		debug($tasks);die;
	}

	/**
	 * Accummulate total time for a user
	 *
	 * Works from this->time, saves to this->userCumm
	 *
	 * @param float $duration
	 */
	private function projectUserCumm($duration){
		if (!isset($this->userCumm[ $this->userName() ])) {
			$this->userCumm[ $this->userName() ] = 0;
		}
		$this->userCumm[ $this->userName() ] += $duration;
	}

	/**
	 * Accummulate total time for a user
	 *
	 * Works from this->time, saves to this->userCumm
	 *
	 * @param float $duration
	 */
	private function userUserCumm($duration){
		if (!isset($this->userCumm['User'][ $this->userName() ])) {
			$this->userCumm['User'][ $this->userName() ]['Time'] = 0;
		}
		$this->userCumm['User'][ $this->userName() ]['Time'] += $duration;
	}

	/**
	 * Accummulate total time for a project
	 *
	 * Works from this->time, saves to this->projectCumm
	 *
	 * @param float $duration
	 */
	private function projectProjectCumm($duration) {
		if (!isset($this->projectCumm['Project'][ $this->projectName() ])) {
			$this->projectCumm['Project'][ $this->projectName() ]['Time'] = 0;
		}
		$this->projectCumm['Project'][ $this->projectName() ]['Time'] += $duration;
	}

	/**
	 * Accummulate total time for a projects task
	 *
	 * Works from this->time, saves to this->projectCumm
	 *
	 * @param float $duration
	 */
	private function projectTaskCumm($duration){
		if (!isset($this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ])) {
			$this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ]['Time'] = 0;
		}
		$this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ]['Time'] += $duration;
	}

	/**
	 * Accummulate total time for a user on a specific project/task
	 *
	 * Works from this->time, saves to this->projectCumm
	 *
	 * @param float $duration
	 */
	private function projectTaskUserCumm($duration) {
		if (!isset($this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ][ $this->userName() ])) {
			$this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ][ $this->userName() ] = 0;
		}
		$this->projectCumm['Project'][ $this->projectName() ]['Task'][ $this->taskName() ][ $this->userName() ] += $duration;
	}

	/**
	 * Accummulate total time for specific project/task by user
	 *
	 * Works from this->time, saves to this->userCumm
	 *
	 * @param float $duration
	 */
	private function userProjectTaskCumm($duration) {
		if (!isset($this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ][ $this->taskName() ])) {
			$this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ][ $this->taskName() ] = 0;
		}
		$this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ][ $this->taskName() ] += $duration;
	}

	/**
	 * Accummulate total time for a user on a project
	 *
	 * Works from this->time, saves to this->projectCumm
	 *
	 * @param float $duration
	 */
	private function projectProjectUserCumm($duration) {
		if (!isset($this->projectCumm['Project'][ $this->projectName() ]['User'][ $this->userName() ]['Time'])) {
			$this->projectCumm['Project'][ $this->projectName() ]['User'][ $this->userName() ]['Time'] = 0;
		}
		$this->projectCumm['Project'][ $this->projectName() ]['User'][ $this->userName() ]['Time'] += $duration;
	}

	/**
	 * Accummulate total time for a project by user
	 *
	 * Works from this->time, saves to this->userCumm
	 *
	 * @param float $duration
	 */
	private function userProjectCumm($duration) {
		if (!isset($this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ]['Time'])) {
			$this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ]['Time'] = 0;
		}
		$this->userCumm['User'][ $this->userName() ]['Project'][ $this->projectName() ]['Time'] += $duration;
	}

	/**
	 * Calculate the duration (in hours) of the current this->time
	 *
	 * Trims to 2 decimal places so there will be slight rounding errors
	 *
	 * @return float
	 */
	private function duration() {
		$dur = explode(':', $this->time['duration']);
//		return (($dur[0] * HOUR) + ($dur[1] * MINUTE) + $dur[2]) / HOUR;
		return number_format((($dur[0] * HOUR) + ($dur[1] * MINUTE) + $dur[2]) / HOUR , 2);
	}

	/**
	 * Return the name of the user linked to this->time
	 *
	 * @return string
	 */
	private function userName() {
		if (is_null($this->time['user_id'])) {
			return ('un-named');
		}
		return  $this->users[$this->time['user_id']];
	}

	/**
	 * Return the name of the task linked to this->time
	 *
	 * @return string
	 */
	private function taskName() {
		if (is_null($this->time['task_id'])) {
			return ('un-named');
		}
		return  $this->tasks[$this->time['task_id']];
	}

	/**
	 * Return the name of the project linked to this->time
	 *
	 * @return string
	 */
	private function projectName() {
		if (is_null($this->time['project_id'])) {
			return 'un-named';
		}
		return  $this->projects[$this->time['project_id']];
	}


}
