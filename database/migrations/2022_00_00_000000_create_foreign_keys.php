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
    		'table' => 'user_reference_code',
    		'fk' => array(
    			[
    				'foreign' => 'unique_code_id',
    				'ref' => 'id',
    				'on' => 'uam_unique_code'
				],
				[
    				'foreign' => 'clinic_user_id',
    				'ref' => 'clinic_user_id',
    				'on' => 'user_information'
    			]
    		)
    	),
		array(
    		'table' => 'user_information',
    		'fk' => array(
    			[
    				'foreign' => 'user_id',
    				'ref' => 'id',
    				'on' => 'users'
				],
    		)
    	),
		array(
    		'table' => 'staff_information',
    		'fk' => array(
    			[
    				'foreign' => 'user_id',
    				'ref' => 'id',
    				'on' => 'users'
				],
    		)
    	),
		array(
    		'table' => 'user_profile_picture',
    		'fk' => array(
    			[
    				'foreign' => 'user_id',
    				'ref' => 'id',
    				'on' => 'users'
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
