<?php

use Illuminate\Database\Seeder;
use \App\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
                [
                    'name' => 'user-create',
                    'display_name' => 'Create User',
                    'description' => 'This created a new Application User',
                    'grouped_id' => '1'
                ],
                [
                    'name' => 'user-edit',
                    'display_name' => 'Edit User',
                    'description' => 'This edits User details',
                    'grouped_id' => '1'
                ],
                [
                    'name' => 'user-delete',
                    'display_name' => 'Delete User',
                    'description' => 'This deletes a User',
                    'grouped_id' => '1'
                ],
                [
                    'name' => 'create-batch',
                    'display_name' => 'Create/Upload Batch',
                    'description' => 'Admin can Create/Upload work batch',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'delete-batch',
                    'display_name' => 'Delete Batch',
                    'description' => 'Deletes batch entirely from system',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'assign-batch-name',
                    'display_name' => 'Assign Batch Name',
                    'description' => 'Assign a batch name after upload',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'edit-batch-name',
                    'display_name' => 'Change Batch Name',
                    'description' => 'Update batch name after upload',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'assign-users-to-batch',
                    'display_name' => 'Assign Work to Freelancers',
                    'description' => 'Assign work to freelancers',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'view-batch',
                    'display_name' => 'View Batch',
                    'description' => 'View batch details',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'download-batch',
                    'display_name' => 'Download Batch',
                    'description' => 'Download Batch when completed',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'notify-users-batch-available',
                    'display_name' => 'Notify Users for Batch Availability',
                    'description' => 'Notify Users for Batch Availability',
                    'grouped_id' => '2'
                ],
                [
                    'name' => 'dashboard-view-batch-name',
                    'display_name' => 'Assign Batch Name',
                    'description' => 'Assign Batch Name to Un-named Uploads',
                    'grouped_id' => '3'
                ],
                [
                    'name' => 'dashboard-view-notify-users',
                    'display_name' => 'Notify Users of Job Available',
                    'description' => 'Notify Users when task is assigned',
                    'grouped_id' => '3'
                ],
                [
                    'name' => 'dashboard-view-list-batch',
                    'display_name' => 'Show list of Available Batches',
                    'description' => 'Shows the list of Available Batches',
                    'grouped_id' => '3'
                ],
                [
                    'name' => 'move-to-saved',
                    'display_name' => 'Move Article back to Saved Status',
                    'description' => 'Move Article back to Saved Status',
                    'grouped_id' => '4'
                ],
                [
                    'name' => 'do-quality-check',
                    'display_name' => 'Do Quality Check',
                    'description' => 'Do Quality Check once Batch is Submitted',
                    'grouped_id' => '4'
                ],
            ];
            
            foreach($permissions as $key => $value)
            {
                Permission::create($value);
            }
    }
}
