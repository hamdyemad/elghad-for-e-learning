<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories and instructors exist
        $categories = Category::all()->keyBy('slug');
        $instructors = User::where('type', 'instructor')->get();

        if ($instructors->count() === 0) {
            $this->command->info('No instructors found. Please run InstructorSeeder first.');
            return;
        }

        if ($categories->count() === 0) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get category IDs with fallbacks
        $categoryIds = [
            'programming' => $categories->get('programming')?->id ?? $categories->first()->id,
            'design' => $categories->get('design')?->id ?? $categories->first()->id,
            'english' => $categories->get('english')?->id ?? $categories->first()->id,
            'marketing' => $categories->get('marketing')?->id ?? $categories->first()->id,
            'business' => $categories->get('business')?->id ?? $categories->first()->id,
            'web-development' => $categories->get('web-development')?->id ?? $categoryIds['programming'] ?? $categories->first()->id,
            'ui-ux' => $categories->get('ui-ux')?->id ?? $categoryIds['design'] ?? $categories->first()->id,
        ];

        $courses = [
            // Web Development Courses
            [
                'title' => 'دورة Laravel للمبتدئين',
                'description' => 'تعلم إطار عمل Laravel من الصفر. تغطي الأساسيات مثل التوجيه،Eloquent ORM،والblade templates.',
                'category_id' => $categoryIds['web-development'],
                'instructor_id' => $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '20 ساعة',
                'price' => 299.99,
                'status' => 'published',
                'is_free' => false,
            ],
            [
                'title' => 'مقدمة في JavaScript',
                'description' => 'أساسيات JavaScript: المتغيرات،الدوال،الأحداث، وإدارة DOM.',
                'category_id' => $categoryIds['web-development'],
                'instructor_id' => $instructors->count() > 1 ? $instructors[1]->id : $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '15 ساعة',
                'price' => 199.99,
                'status' => 'published',
                'is_free' => false,
            ],
            [
                'title' => 'React avanzado',
                'description' => 'تعلم React بمستوى متقدم: Hooks،Redux، و Next.js.',
                'category_id' => $categoryIds['web-development'],
                'instructor_id' => $instructors->count() > 2 ? $instructors[2]->id : $instructors->first()->id,
                'level' => 'advanced',
                'duration' => '25 ساعة',
                'price' => 399.99,
                'status' => 'published',
                'is_free' => false,
            ],
            // Design Courses
            [
                'title' => 'أساسيات Photoshop',
                'description' => 'تعلم أدوات Photoshop الأساسية للمبتدئين. من التصميم الجرافيكي إلى معالجة الصور.',
                'category_id' => $categoryIds['design'],
                'instructor_id' => $instructors->count() > 3 ? $instructors[3]->id : $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '12 ساعة',
                'price' => 149.99,
                'status' => 'published',
                'is_free' => false,
            ],
            [
                'title' => 'تصميم واجهات المستخدم UI/UX',
                'description' => 'تعلم مبادئ تصميم واجهات المستخدم وتجربة المستخدم باستخدام Figma.',
                'category_id' => $categoryIds['ui-ux'],
                'instructor_id' => $instructors->first()->id,
                'level' => 'intermediate',
                'duration' => '18 ساعة',
                'price' => 249.99,
                'status' => 'published',
                'is_free' => false,
            ],
            // English Courses
            [
                'title' => 'الإنجليزية للمبتدئين',
                'description' => 'دورة شاملة في اللغة الإنجليزية للمبتدئين. من القواعد الأساسية إلى المحادثة البسيطة.',
                'category_id' => $categoryIds['english'],
                'instructor_id' => $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '30 ساعة',
                'price' => 0,
                'status' => 'published',
                'is_free' => true,
            ],
            [
                'title' => 'IELTS Preparation',
                'description' => 'تحضير كامل لامتحان IELTS مع استراتيجيات النجاح ونماذج اختبارات حقيقية.',
                'category_id' => $categoryIds['english'],
                'instructor_id' => $instructors->count() > 1 ? $instructors[1]->id : $instructors->first()->id,
                'level' => 'intermediate',
                'duration' => '40 ساعة',
                'price' => 349.99,
                'status' => 'published',
                'is_free' => false,
            ],
            // Marketing Courses
            [
                'title' => 'التسويق الرقمي الشامل',
                'description' => 'تعلم جميع جوانب التسويق الرقمي: SEO،SEM،التسويق عبر وسائل التواصل،والبريد الإلكتروني.',
                'category_id' => $categoryIds['marketing'],
                'instructor_id' => $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '35 ساعة',
                'price' => 279.99,
                'status' => 'published',
                'is_free' => false,
            ],
            [
                'title' => 'إعلانات Facebook & Instagram',
                'description' => 'إتقان إنشاء وإدارة الحملات الإعلانية على فيسبوك وإنستجرام.',
                'category_id' => $categoryIds['marketing'],
                'instructor_id' => $instructors->count() > 2 ? $instructors[2]->id : $instructors->first()->id,
                'level' => 'intermediate',
                'duration' => '10 ساعات',
                'price' => 179.99,
                'status' => 'published',
                'is_free' => false,
            ],
            // Business Courses
            [
                'title' => 'مهارات القيادة',
                'description' => 'تطوير مهارات القيادة والإدارة في بيئات العمل الحديثة.',
                'category_id' => $categoryIds['business'],
                'instructor_id' => $instructors->first()->id,
                'level' => 'intermediate',
                'duration' => '8 ساعات',
                'price' => 199.99,
                'status' => 'published',
                'is_free' => false,
            ],
            [
                'title' => 'إدارة المشاريع',
                'description' => 'أساسيات إدارة المشاريع باستخدام منهجيات Agile و Scrum.',
                'category_id' => $categoryIds['business'],
                'instructor_id' => $instructors->count() > 3 ? $instructors[3]->id : $instructors->first()->id,
                'level' => 'beginner',
                'duration' => '15 ساعة',
                'price' => 229.99,
                'status' => 'published',
                'is_free' => false,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );
        }
    }
}
