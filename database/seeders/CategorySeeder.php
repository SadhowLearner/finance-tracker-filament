<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Salary', 'type' => 'income', 'notes' => 'Monthly salary', 'user_id' => 1],
            ['name' => 'Freelance', 'type' => 'income', 'notes' => 'Project-based income', 'user_id' => 1],
            ['name' => 'Groceries', 'type' => 'expense', 'notes' => 'Daily food expenses', 'user_id' => 1],
            ['name' => 'Transport', 'type' => 'expense', 'notes' => 'Public transport fees', 'user_id' => 1],
            ['name' => 'Salary', 'type' => 'income', 'notes' => 'Monthly salary', 'user_id' => 1],
            ['name' => 'Freelance', 'type' => 'income', 'notes' => 'Project-based income', 'user_id' => 1],
            ['name' => 'Groceries', 'type' => 'expense', 'notes' => 'Daily food expenses', 'user_id' => 1],
            ['name' => 'Transport', 'type' => 'expense', 'notes' => 'Public transport fees', 'user_id' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
