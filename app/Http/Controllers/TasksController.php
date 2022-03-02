<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\task;
use App\Models\Assigned_task;
use App\Http\Controllers\AssignedToController;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Money\Exchange;

use function PHPUnit\Framework\isEmpty;

class TasksController extends Controller
{
    //add row 
    function Insert($data, $milestone_id)

    {
        try {
            $AssignedTo = new AssignedToController;
            foreach ($data as $task) {
                $arr = array(
                    "milestone_id" => $milestone_id,
                    "name" => $task['task_name'],
                    "description" => $task['task_description'],
                );
                $tasks = task::create($arr);
                $assignees = $AssignedTo->Insert($task['task_assignees'], $tasks->id);
                if ($assignees['code'] == 500) {
                    return ['code' => 500, 'msg' => $assignees['msg']];
                }
                return ['code' => 200, 'msg' => 'successful'];
            }
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => 'tasks controller: ' . $error->getMessage()];
        }
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    function getTaskByMilestoneId($id)
    {
        $AssignedTo = new AssignedToController;
        $tasks = task::select('id', 'name', 'description')->where('milestone_id', $id)->get();
        $tasks_details = [];

        foreach ($tasks as $task) {
            $assigned = $AssignedTo->getAssignedByTaskId($task->id);
            array_push($tasks_details, array(
                "task_id" => $task->id,
                "task_name" => $task->name,
                "task_description" => $task->description,
                "task_assignees" => ($assigned),
            ));
        }
        return ($tasks_details);
    }
    function updateTasksByMileStoneId($data, $milestone_id)
    {
        $del_tasks = task::where('milestone_id', $milestone_id)->delete();

        $AssignedTo = new AssignedToController;
        foreach ($data as $task) {
            $arr = array(
                "milestone_id" => $milestone_id,
                "name" => $task['name'],
                "description" => $task['description'],
            );
            $tasks = task::create($arr);

            $AssignedTo->Insert($task['assignedTo'], $tasks->id);
        }
    }
    function deleteTasksByMilestoneId($id)
    {
        try {
            $assignedToObj = new AssignedToController;
            $tasks_ids = task::where('milestone_id', '=', $id)
                ->select('id')
                ->get();

            if ($tasks_ids->isEmpty()) {
                return ['code' => 201, "msg" => 'no tasks'];
            } else {
                foreach ($tasks_ids as $task_id) {
                    // dd($task_id->id);
                    Assigned_task::where('task_id', $task_id->id)->delete();
                    task::where('id', $task_id->id)->delete();
                    return ['code' => 200, "msg" => 'successful'];
                }
            }
        } catch (Exception $error) {
            return ['code' => 500, "msg" => $error->getMessage()];
        }
    }
}
