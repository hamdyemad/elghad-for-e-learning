<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create parent categories
        $parentCategories = [
            [
                'name' => 'البرمجة',
                'slug' => 'programming',
                'description' => 'دورات البرمجة وتطوير البرمجيات',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'التصميم',
                'slug' => 'design',
                'description' => 'دورات التصميم الجرافيكي والتصميم الرقمي',
                'order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'اللغة الإنجليزية',
                'slug' => 'english',
                'description' => 'دورات تعليم اللغة الإنجليزية',
                'order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'التسويق',
                'slug' => 'marketing',
                'description' => 'دورات التسويق الرقمي وإدارة الحملات',
                'order' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'الأعمال',
                'slug' => 'business',
                'description' => 'دورات في إدارة الأعمال والمهارات المهنية',
                'order' => 5,
                'status' => 'active',
            ],
        ];

        // Store parent IDs for child categories
        $parentIds = [];

        foreach ($parentCategories as $category) {
            $parent = Category::updateOrCreate(['slug' => $category['slug']], $category);
            $parentIds[$category['slug']] = $parent->id;
        }

        // Child categories
        $childCategories = [
            // Programming subcategories
            [
                'name' => 'تطوير الويب',
                'slug' => 'web-development',
                'description' => 'تطوير مواقع وتطبيقات الويب',
                'parent_id' => $parentIds['programming'] ?? null,
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'تطوير التطبيقات',
                'slug' => 'mobile-development',
                'description' => 'تطوير تطبيقات الهواتف الذكية',
                'parent_id' => $parentIds['programming'] ?? null,
                'order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'الذكاء الاصطناعي',
                'slug' => 'ai',
                'description' => 'تعلم الآلة والذكاء الاصطناعي',
                'parent_id' => $parentIds['programming'] ?? null,
                'order' => 3,
                'status' => 'active',
            ],
            // Design subcategories
            [
                'name' => 'تصميم واجهات UI/UX',
                'slug' => 'ui-ux',
                'description' => 'تصميم واجهات المستخدم وتجربة المستخدم',
                'parent_id' => $parentIds['design'] ?? null,
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'الموشن جرافيك',
                'slug' => 'motion-graphics',
                'description' => 'تصميم الأنيميشن والمؤثرات البصرية',
                'parent_id' => $parentIds['design'] ?? null,
                'order' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($childCategories as $category) {
            if ($category['parent_id']) {
                Category::updateOrCreate(['slug' => $category['slug']], $category);
            }
        }
    }
}
