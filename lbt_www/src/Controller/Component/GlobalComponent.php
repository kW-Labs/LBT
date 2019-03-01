<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * @property \Cake\ORM\Table Logs
 * @property \Cake\ORM\Table TableObj
 * @property \Cake\ORM\Table Users
 */
class GlobalComponent extends Component
{
    public function saveLog($user_id, $title, $item_id, $platform, $type, $action, $error = false, $results, $execute_time = null, $total_time = null)
    {

        $logsTable = TableRegistry::getTableLocator()->get('Logs');
        $log = $logsTable->newEntity();
        $log_arr = ["user_id" => $user_id,
            "title" => $title,
            "item_id" => $item_id,
            "platform" => $platform,
            "table" => $type,
            "action" => $action,
            "error" => $error,
            "results" => $results,
            "execute_time" => $execute_time,
            "total_time" => $total_time];
        $log = $logsTable->patchEntity($log, $log_arr);

        if ($logsTable->save($log)) {
            return true;
        }

        return false;
    }

}