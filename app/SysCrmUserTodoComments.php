<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTodoComments extends Model
{
    protected $table = 'sys_crm_user_todo_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'todo_id',
        'todo_item_id',
        'user_id',
        'comment',
        'created_at',
        'updated_at',
        'status'
    ];

    public function todo()
    {
        return $this->belongsTo('App\SysCrmUserTodo', 'todo_id', 'id');
    }

    
    public function todoItem()
    {
        return $this->belongsTo('App\SysCrmUserTodoItems', 'todo_item_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }


}

// CREATE TABLE `sys_crm_user_todo_comments` (
//     `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     `todo_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sys_crm_user_tasks',
//     `todo_item_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Foreign key from sys_crm_user_task_items',
//     `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sm_staffs',
//     `comment` TEXT DEFAULT NULL,
//    `status` ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started' COMMENT 'todo status',
//    `deleted_at` DATETIME  NULL DEFAULT NULL,
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL,
//     CONSTRAINT fk_todo_comments_task FOREIGN KEY (todo_id) REFERENCES sys_crm_user_todos(id) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

