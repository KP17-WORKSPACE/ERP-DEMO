<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTodo extends Model
{
   
    protected $table = 'sys_crm_user_todos';
    protected $primaryKey = 'id';



    protected $fillable = [
        'id',
        'todo_title',
        'todo_due_date',
        'priority',
        'status',
        'description',
        'attachment',
        'user_id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];


    public function todoItems()
    {
        return $this->hasMany('App\SysCrmUserTodoItems', 'todo_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }


    // In Task model
    public function getIsOverdueAttribute()
    {
        return \Carbon\Carbon::parse($this->todo_due_date)->isPast();
    }



}

// CREATE TABLE `sys_crm_user_todos` (
//    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    `todo_title` TEXT NOT NULL COMMENT 'Main title of the todo',
//    `todo_due_date` DATETIME DEFAULT NULL COMMENT 'Due date/time for todo completion',
//    `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT NULL COMMENT 'todo priority level',
//    `status` ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started' COMMENT 'todo status',
//    `description` TEXT NULL COMMENT 'Detailed description of the todo',
//    `attachment` varchar(255) DEFAULT NULL COMMENT 'File path or URL',
//    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'User ID',
//    `deleted_at` DATETIME  NULL DEFAULT NULL,
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
