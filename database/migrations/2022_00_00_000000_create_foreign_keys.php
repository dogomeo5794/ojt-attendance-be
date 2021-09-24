<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private $tables = array(
    	array(
    		'table' => 'office_account',
    		'fk' => array(
    			[
    				'foreign' => 'user_id',
    				'ref' => 'id',
    				'on' => 'users'
				],
				[
    				'foreign' => 'office_detail_id',
    				'ref' => 'id',
    				'on' => 'office_details'
    			]
    		)
    	),
		array(
    		'table' => 'admin_account',
    		'fk' => array(
    			[
    				'foreign' => 'user_id',
    				'ref' => 'id',
    				'on' => 'users'
				],
    		)
    	),
		array(
    		'table' => 'attendance',
    		'fk' => array(
    			[
    				'foreign' => 'office_account_id',
    				'ref' => 'id',
    				'on' => 'office_account'
				],
				[
    				'foreign' => 'student_information_id',
    				'ref' => 'id',
    				'on' => 'student_information'
				],
    		)
    	),
		array(
    		'table' => 'ojt_office',
    		'fk' => array(
    			[
    				'foreign' => 'student_information_id',
    				'ref' => 'id',
    				'on' => 'student_information'
				],
				[
    				'foreign' => 'office_detail_id',
    				'ref' => 'id',
    				'on' => 'office_details'
				],
    		)
    	),

		array(
    		'table' => 'generated_qrcode',
    		'fk' => array(
    			[
    				'foreign' => 'student_information_id',
    				'ref' => 'id',
    				'on' => 'student_information'
				],
    		)
    	),
    );

    public function up()
    {
    	foreach ($this->tables as $tbl) {
    		Schema::table("{$tbl['table']}", function (Blueprint $table) use ($tbl) {
    			foreach ($tbl['fk'] as $fk) {
    				$table->foreign("{$fk['foreign']}")
    				->references("{$fk['ref']}")
    				->on("{$fk['on']}")
    				->onUpdate('cascade')
    				->onDelete('cascade');
    			}
    		});
    	}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	foreach($this->tables as $key => $tbl) {
    		Schema::table("{$tbl['table']}", function (Blueprint $table) use ($tbl) {
    			foreach($tbl['fk'] as $fk) {
    				$table->dropForeign(["{$fk['foreign']}"]);
    			}
    		});
    	}
    }
  }
