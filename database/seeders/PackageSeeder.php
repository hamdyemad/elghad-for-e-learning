<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Course;
use App\Models\Category;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->count() === 0) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $courses = Course::where('status', 'published')->get();

        if ($courses->count() < 3) {
            $this->command->info('Not enough published courses. Please run CourseSeeder first.');
            return;
        }

        $packages = [
            // Web Development Bundle
            [
                'title' => 'حزمة تطوير الويب الكاملة',
                'description' => 'احصل على جميع دورات تطوير الويب: Laravel، JavaScript، React، و Vue.js. قيمتها أكثر من 1000$ بسعر خاص!',
                'category_id' => $categories->where('slug', 'web-development')->first()?->id ?? $categories->first()->id,
                'price' => 699.99,
                'status' => 'published',
            ],
            // Design Bundle
            [
                'title' => 'حزمة التصميم الشاملة',
                'description' => 'جميع دورات التصميم: Photoshop، Illustrator، Figma، والموشن جرافيك. من الصفر إلى الاحتراف.',
                'category_id' => $categories->where('slug', 'ui-ux')->first()?->id ?? ($categories->where('slug', 'design')->first()?->id ?? $categories->first()->id),
                'price' => 499.99,
                'status' => 'published',
            ],
            // English Bundle
            [
                'title' => 'حزمة إتقان الإنجليزية',
                'description' => 'من المبتدئ إلى المحترف: شامل جميع مستويات الإنجليزية بما في ذلك تحضير IELTS.',
                'category_id' => $categories->where('slug', 'english')->first()?->id ?? $categories->first()->id,
                'price' => 399.99,
                'status' => 'published',
            ],
            // Marketing Bundle
            [
                'title' => 'حزمة التسويق الرقمي',
                'description' => 'احترف التسويق الرقمي: SEO، SEM، التسويق عبر السوشيال ميديا، والتسويق عبر البريد الإلكتروني.',
                'category_id' => $categories->where('slug', 'marketing')->first()?->id ?? $categories->first()->id,
                'price' => 549.99,
                'status' => 'published',
            ],
            // Career Development Bundle
            [
                'title' => 'حزمة تطوير المهارات المهنية',
                'description' => 'مهارات القيادة، إدارة المشاريع، والتواصل الفعال. كل ما تحتاجه للنجاح في حياتك المهنية.',
                'category_id' => $categories->where('slug', 'business')->first()?->id ?? $categories->first()->id,
                'price' => 349.99,
                'status' => 'published',
            ],
            // Free Starter Pack
            [
                'title' => 'باك مجاني للمبتدئين',
                'description' => 'بداية مثالية مع مجموعة من الدورات المجانية لتجربة التعلم على منصتنا.',
                'category_id' => $categories->first()->id,
                'price' => 0,
                'status' => 'published',
            ],
        ];

        // Create packages
        foreach ($packages as $packageData) {
            Package::updateOrCreate(
                ['title' => $packageData['title']],
                $packageData
            );
        }

        // Attach courses to packages
        $webDevPackage = Package::where('title', 'حزمة تطوير الويب الكاملة')->first();
        $designPackage = Package::where('title', 'حزمة التصميم الشاملة')->first();
        $englishPackage = Package::where('title', 'حزمة إتقان الإنجليزية')->first();
        $marketingPackage = Package::where('title', 'حزمة التسويق الرقمي')->first();
        $businessPackage = Package::where('title', 'حزمة تطوير المهارات المهنية')->first();
        $freePackage = Package::where('title', 'باك مجاني للمبتدئين')->first();

        // Web Development courses
        if ($webDevPackage) {
            $webDevCourses = $courses->where('category_id', $webDevPackage->category_id)->take(4);
            foreach ($webDevCourses as $course) {
                $webDevPackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }

        // Design courses
        if ($designPackage) {
            $designCourses = $courses->where('category_id', $designPackage->category_id)->take(3);
            foreach ($designCourses as $course) {
                $designPackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }

        // English courses
        if ($englishPackage) {
            $englishCourses = $courses->where('category_id', $englishPackage->category_id)->take(2);
            foreach ($englishCourses as $course) {
                $englishPackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }

        // Marketing courses
        if ($marketingPackage) {
            $marketingCourses = $courses->where('category_id', $marketingPackage->category_id)->take(3);
            foreach ($marketingCourses as $course) {
                $marketingPackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }

        // Business courses
        if ($businessPackage) {
            $businessCourses = $courses->where('category_id', $businessPackage->category_id)->take(3);
            foreach ($businessCourses as $course) {
                $businessPackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }

        // Free package - attach a couple of free courses
        if ($freePackage) {
            $freeCourses = $courses->where('is_free', true)->take(2);
            if ($freeCourses->count() === 0) {
                $freeCourses = $courses->take(2);
            }
            foreach ($freeCourses as $course) {
                $freePackage->courses()->syncWithoutDetaching([$course->id]);
            }
        }
    }
}
