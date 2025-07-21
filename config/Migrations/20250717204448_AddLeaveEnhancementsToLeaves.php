<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLeaveEnhancementsToLeaves extends AbstractMigration
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

    if (!$table->hasColumn('leave_category_id')) {
        $table
            ->addColumn('leave_category_id', 'integer', ['null' => true])
            ->addForeignKey('leave_category_id', 'leave_categories', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION']);
    }

    if (!$table->hasColumn('total_days')) {
        $table->addColumn('total_days', 'integer', ['default' => 0, 'null' => false]);
    }

    if (!$table->hasColumn('deduct_from_annual')) {
        $table->addColumn('deduct_from_annual', 'boolean', ['default' => false, 'null' => false]);
    }

    if (!$table->hasColumn('is_unpaid')) {
        $table->addColumn('is_unpaid', 'boolean', ['default' => false, 'null' => false]);
    }

    $table->update();

    }
}

