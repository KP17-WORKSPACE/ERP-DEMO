<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTodoItems extends Model
{

    protected $table = 'sys_crm_user_todo_items';
    protected $primaryKey = 'id';


    protected $fillable = [
        'id',
        'todo_id',
        'todo',
        'status',
        'created_at',
        'updated_at'
    ];

    public function todo()
    {
        return $this->belongsTo('App\SysCrmUserTodo', 'todo_id', 'id');
    }

  





}
// CREATE TABLE `sys_crm_user_todo_items` (
//    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    `todo_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sys_crm_user_todos',
//    `todo` TEXT NOT NULL COMMENT 'Todo item',
//    `status` ENUM('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started' COMMENT 'todo status',
//    `deleted_at` DATETIME  NULL DEFAULT NULL,
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL,
//    CONSTRAINT fk_user_todo_items_todo_id  FOREIGN KEY (todo_id)
//     REFERENCES sys_crm_user_todos(id)
//     ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;