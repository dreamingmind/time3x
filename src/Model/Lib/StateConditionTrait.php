<?php
namespace App\Model\Lib;

/**
 * Project and Task states
 */
define("ACTIVE", 'active');
define("INACTIVE", 'inactive');
define("MAINTENANCE", 'maintenance');


trait StateConditionTrait
{
    /**
     * Make the conditions to find projects/tasks at a state
     *
     * @param string $type
     * @return array conditions to find the desired states
     */
    public function stateCondition($type = 'all') {
        $alias = $this->getAlias();
        switch ($type) {
            case 'jobs':
                $condition = array('OR' => array(
                    array("$alias.state" => ACTIVE),
                    array("$alias.state" => MAINTENANCE),
                ));
                break;
            case 'active' :
                $condition = array("$alias.state" => ACTIVE);
                break;
            case 'inactive' :
                $condition = array("$alias.state" => INACTIVE);
                break;
            case 'maintenance' :
                $condition = array("$alias.state" => MAINTENANCE);
                break;
            default:
                $condition = array();
                break;
        }
        return $condition;
    }
}
