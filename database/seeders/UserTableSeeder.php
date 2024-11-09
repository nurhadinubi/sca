<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\Division;
use App\Models\Doctype;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		User::insert([
			[
				"email" => "test.nurhadi@gmail.com",
				"username" => "test.nurhadi",
				"password" => bcrypt(1234567890),
				"name" => "Nurhadi"
			],
			[
				"email" => "test2@gmail.com",
				"username" => "test2",
				"password" => bcrypt(1234567890),
				"name" => "Nurhadi2"
			],
			[
				"email" => "test3@gmail.com",
				"username" => "test3",
				"password" => bcrypt(1234567890),
				"name" => "Nurhadi 3"
			],
			[
				"email" => "test4@gmail.com",
				"username" => "test4",
				"password" => bcrypt(1234567890),
				"name" => "Nurhadi 4"
			],
			[
				"email" => "test5@gmail.com",
				"username" => "test5",
				"password" => bcrypt(1234567890),
				"name" => "Nurhadi 5"
			]
		]);

		Doctype::insert([
			[
				'doctype' => 'DRY',
				'description' => 'Tipe Dokumen DRY',
			],

			[
				'doctype' => 'FROZEN',
				'description' => 'Tipe Dokumen FROZEN',
			],
			[
				'doctype' => 'LIQUID',
				'description' => 'Tipe Dokumen LIQUID',
			],
			[
				'doctype' => 'SNACK',
				'description' => 'Tipe Dokumen SNACK',
			],
		]);

		Division::insert([
			[
				'division_code' => 'DRY',
				'division_description' => 'Tipe Dokumen DRY',
			],

			[
				'division_code' => 'FROZEN',
				'division_description' => 'Tipe Dokumen FROZEN',
			],
			[
				'division_code' => 'LIQUID',
				'division_description' => 'Tipe Dokumen LIQUID',
			],
			[
				'division_code' => 'SNACK',
				'division_description' => 'Tipe Dokumen SNACK',
			],
		]);

		// Approval::insert([
		// 	[
		// 		'user_id' => 2,
		// 		'level' => 1,
		// 		'doctype_id' => 1,
		// 		'email' => 'aproval1@gmail.com',
		// 	],
		// 	[
		// 		'user_id' => 2,
		// 		'level' => 1,
		// 		'doctype_id' => 2,
		// 		'email' => 'aproval1@gmail.com',
		// 	],
		// 	[
		// 		'user_id' => 4,
		// 		'level' => 1,
		// 		'doctype_id' => 2,
		// 		'email' => 'aproval3@gmail.com',
		// 	],
		// 	[
		// 		'user_id' => 3,
		// 		'level' => 2,
		// 		'doctype_id' => 3,
		// 		'email' => 'aproval2@gmail.com',
		// 	],
		// 	[
		// 		'user_id' => 4,
		// 		'level' => 2,
		// 		'doctype_id' => 3,
		// 		'email' => 'aproval2@gmail.com',
		// 	],
		// 	[
		// 		'user_id' => 5,
		// 		'level' => 2,
		// 		'doctype_id' => 1,
		// 		'email' => 'aproval5@gmail.com',
		// 	]
		// ]);
	}
}
