<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(ImagesTableSeeder::class);
         $this->call(VideosTableSeeder::class);
         $this->call(TagsTableSeeder::class);
         $this->call(CategoriesTableSeeder::class);
         $this->call(WorksTableSeeder::class);
         $this->call(CommentsTableSeeder::class);
         $this->call(FavoursTableSeeder::class);
    }
}
