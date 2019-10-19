<?php
namespace App\Lib;

use App\Model\Entity\Time;
use Cake\ORM\TableRegistry;

/**
 * ReportComponent
 *
 * Provide report analyses for time records
 *
 * @author dondrake
 */
class Summaries {

	/**
	 * The current time record of interest
	 *
	 * This is a transient value. Be sure you set it when you need it.
	 * Field names are first level indexes.
	 *
	 * @var Time
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
			$duration = $this->duration();
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
			$duration = $this->duration();
			$this->projectUserCumm($duration);
			$this->projectProjectCumm($duration);
			$this->projectProjectUserCumm($duration);
			$this->projectTaskCumm($duration);
			$this->projectTaskUserCumm($duration);
		}
		return $this->projectCumm;
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
        $this->projects = $projects->find('list')->toArray();
		$this->users = $users->find('list')->toArray();
		$this->tasks = $tasks->find('list')->toArray();
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
//		$dur = explode(':', $this->time['duration']);
//		return (($dur[0] * HOUR) + ($dur[1] * MINUTE) + $dur[2]) / HOUR;
		return number_format($this->time->duration() / HOUR , 2);
	}

	/**
	 * Return the name of the user linked to this->time
	 *
	 * @return string
	 */
	private function userName() {
	    return is_null($this->time->userId())
            ? 'un-named'
            : $this->users[$this->time->userId()];
	}

	/**
	 * Return the name of the task linked to this->time
	 *
	 * @return string
	 */
	private function taskName() {
        return is_null($this->time->taskId())
            ? 'un-named'
            : $this->tasks[$this->time->taskId()];
	}

	/**
	 * Return the name of the project linked to this->time
	 *
	 * @return string
	 */
	private function projectName() {
        return is_null($this->time->projectId())
            ? 'un-named'
            : $this->projects[$this->time->projectId()];
	}


}
