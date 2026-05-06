<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTaskComments extends Model
{
    protected $table = 'sys_crm_user_task_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'task_id',
        'task_item_id',
        'user_id',
        'is_read_sender',
        'is_read_receiver',
        'comment',
        'created_at',
        'updated_at'
    ];

    public function task()
    {
        return $this->belongsTo('App\SysCrmUserTask', 'task_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\SmStaff', 'user_id', 'user_id');
    }


}

// CREATE TABLE `sys_crm_user_task_comments` (
//      `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     `task_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sys_crm_user_tasks',
//     `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key from sm_staffs',
//     `comment` TEXT DEFAULT NULL,
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL,
//     CONSTRAINT fk_task_comments_task FOREIGN KEY (task_id) REFERENCES sys_crm_user_tasks(id) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
