<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmUserTask extends Model
{
    protected $table = 'sys_crm_user_tasks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'assigned_to',
        'assigned_by',
        'company_id',
        'task_id',
        'task_title',
        'attachment',
        'task_due_date',
        'priority',
        'status_r',
        'status_s',
        'description',
        'created_at',
        'updated_at'
    ];

    public function assignedby()
    {
        return $this->belongsTo('App\SmStaff', 'assigned_by', 'user_id');
    }

    public function assignedto()
    {
        return $this->belongsTo('App\SmStaff', 'assigned_to', 'user_id');
    }

    public function taskitems()
    {
        return $this->hasMany('App\SysCrmUserTaskItems', 'task_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\SysCrmUserTaskComments', 'task_id', 'id')->whereNull('task_item_id');
    }

    public function company()
    {
        return $this->belongsTo('App\SysCompany', 'company_id', 'id');
    }

    // In Task model
    public function getIsOverdueAttribute()
    {
        return \Carbon\Carbon::parse($this->task_due_date)->isPast();
    }



}

// CREATE TABLE `sys_crm_user_tasks` (
//    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    `assigned_to` BIGINT UNSIGNED NOT NULL COMMENT 'User ID of the assignee',
//    `assigned_by` BIGINT UNSIGNED NOT NULL COMMENT 'User ID of the assigner',
//    `task_id` TEXT NOT NULL UNIQUE COMMENT 'task ID starting from TAS-1001',
//    `task_title` TEXT NOT NULL COMMENT 'Main title of the task',
//    `attachment` varchar(255) DEFAULT NULL COMMENT 'File path or URL',
//    `task_due_date` DATETIME DEFAULT NULL COMMENT 'Due date/time for task completion',
//    `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT NULL COMMENT 'Task priority level',
//    `status_r` ENUM('not_started', 'in_progress', 'completed', 'blocked', 'cancelled') NOT NULL DEFAULT 'not_started' COMMENT 'Receiver-side status',
//    `status_s` ENUM('open', 'review', 'approved', 'archived') NOT NULL DEFAULT 'open' COMMENT 'Assigner/admin-side status',
//    `description` TEXT NULL COMMENT 'Detailed description of the task',
//    `created_at` timestamp NULL DEFAULT NULL,
//    `updated_at` timestamp NULL DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;