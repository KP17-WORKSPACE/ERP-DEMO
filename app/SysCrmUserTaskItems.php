<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTaskItems extends Model
{
    protected $table = 'sys_crm_user_task_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'task_id',
        'attachment',
        'task',
        'status_r',
        'status_s',
        'created_at',
        'updated_at'
    ];

    public function task()
    {
        return $this->belongsTo('App\SysCrmUserTask', 'task_id', 'id');
    }

    // UserTaskItem.php
    public function comments()
    {
        return $this->hasMany('App\SysCrmUserTaskComments', 'task_item_id', 'id');
    }





}
// CREATE TABLE `sys_crm_user_task_items` (
//    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    `task_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sys_crm_user_tasks',
//    `task` TEXT NOT NULL COMMENT 'Task item',
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL,
//    CONSTRAINT fk_user_task_items_task_id  FOREIGN KEY (task_id)
//     REFERENCES sys_crm_user_tasks(id)
//     ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;