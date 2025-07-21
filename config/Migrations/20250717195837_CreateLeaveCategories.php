<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateLeaveCategories extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('leaves');

        // Add leave_category_id column with foreign key
        if (!$table->hasColumn('leave_category_id')) {
            $table->addColumn('leave_category_id', 'integer', ['null' => true, 'after' => 'leave_type_id']);
            $table->addForeignKey('leave_category_id', 'leave_categories', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ]);
        }

        // Add total_days column
        if (!$table->hasColumn('total_days')) {
            $table->addColumn('total_days', 'integer', [
                'default' => 0,
                'null' => false,
                'after' => 'end_date',
            ]);
        }

        // Add deduct_from_annual column
        if (!$table->hasColumn('deduct_from_annual')) {
            $table->addColumn('deduct_from_annual', 'boolean', [
                'default' => false,
                'null' => false,
                'after' => 'total_days',
            ]);
        }

        // Add is_unpaid column
        if (!$table->hasColumn('is_unpaid')) {
            $table->addColumn('is_unpaid', 'boolean', [
                'default' => false,
                'null' => false,
                'after' => 'deduct_from_annual',
            ]);
        }

        $table->update();
    }
}

