<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->count() === 0) {
            $this->command->info('No courses found. Please run CourseSeeder first.');
            return;
        }

        $lessonTemplates = [
            // Programming courses
            'programming' => [
                ['topic' => 'المقدمة', 'title' => 'مقدمة في الدورة', 'outsource_type' => 'vimeo', 'duration' => 600, 'is_free' => true],
                ['topic' => 'الإعداد والتهيئة', 'title' => 'تهيئة البيئة والتثبيت', 'outsource_type' => 'vimeo', 'duration' => 900, 'is_free' => true],
                ['topic' => 'الأساسيات', 'title' => 'أساسيات اللغة/الإطار', 'outsource_type' => 'vimeo', 'duration' => 1200, 'is_free' => false],
                ['topic' => 'الهيكلية', 'title' => 'هيكلة المشروع', 'outsource_type' => 'vimeo', 'duration' => 1500, 'is_free' => false],
                ['topic' => 'المتقدمة', 'title' => 'مفاهيم متقدمة', 'outsource_type' => 'vimeo', 'duration' => 1800, 'is_free' => false],
            ],
            // Design courses
            'design' => [
                ['topic' => 'نظرة عامة', 'title' => 'مقدمة في عالم التصميم', 'outsource_type' => 'vimeo', 'duration' => 300, 'is_free' => true],
                ['topic' => 'الأدوات', 'title' => 'تعلم أدوات البرنامج', 'outsource_type' => 'vimeo', 'duration' => 1200, 'is_free' => false],
                ['topic' => 'التصميم', 'title' => 'مبادئ التصميم', 'outsource_type' => 'vimeo', 'duration' => 1500, 'is_free' => false],
                ['topic' => 'التصدير', 'title' => 'تصدير الملفات', 'outsource_type' => 'vimeo', 'duration' => 600, 'is_free' => false],
            ],
            // English courses
            'english' => [
                ['topic' => 'الوحدة 1', 'title' => 'المستوى الأول: التعارف', 'outsource_type' => 'firebase', 'duration' => 1800, 'is_free' => true],
                ['topic' => 'الوحدة 2', 'title' => 'القواعد الأساسية', 'outsource_type' => 'firebase', 'duration' => 1500, 'is_free' => false],
                ['topic' => 'الوحدة 3', 'title' => 'المحادثة اليومية', 'outsource_type' => 'firebase', 'duration' => 2000, 'is_free' => false],
                ['topic' => 'الوحدة 4', 'title' => 'الاستماع والفهم', 'outsource_type' => 'firebase', 'duration' => 1800, 'is_free' => false],
                ['topic' => 'الوحدة 5', 'title' => 'الكتابة والتعبير', 'outsource_type' => 'firebase', 'duration' => 2000, 'is_free' => false],
            ],
            // Marketing courses
            'marketing' => [
                ['topic' => 'مدخل إلى التسويق', 'title' => 'ما هو التسويق الرقمي؟', 'outsource_type' => 'vimeo', 'duration' => 900, 'is_free' => true],
                ['topic' => 'SEO', 'title' => 'تحسين محركات البحث', 'outsource_type' => 'vimeo', 'duration' => 1200, 'is_free' => false],
                ['topic' => 'Social Media', 'title' => 'التسويق عبر السوشيال ميديا', 'outsource_type' => 'vimeo', 'duration' => 1800, 'is_free' => false],
                ['topic' => 'Email Marketing', 'title' => 'التسويق عبر البريد الإلكتروني', 'outsource_type' => 'vimeo', 'duration' => 1500, 'is_free' => false],
                ['topic' => 'Analytics', 'title' => 'تحليل البيانات والتقارير', 'outsource_type' => 'vimeo', 'duration' => 1200, 'is_free' => false],
            ],
            // Business courses
            'business' => [
                ['topic' => 'المقدمة', 'title' => 'مقدمة في الإدارة', 'outsource_type' => 'vimeo', 'duration' => 600, 'is_free' => true],
                ['topic' => 'التخطيط', 'title' => 'التخطيط الاستراتيجي', 'outsource_type' => 'vimeo', 'duration' => 1200, 'is_free' => false],
                ['topic' => 'القيادة', 'title' => 'مهارات القيادة الفعالة', 'outsource_type' => 'vimeo', 'duration' => 1500, 'is_free' => false],
            ],
        ];

        foreach ($courses as $index => $course) {
            $category = $course->category;
            $categorySlug = $category ? $category->slug : 'programming';

            // Determine which template to use based on category
            $templateKey = 'programming';
            if (str_contains($categorySlug, 'design')) {
                $templateKey = 'design';
            } elseif (str_contains($categorySlug, 'english')) {
                $templateKey = 'english';
            } elseif (str_contains($categorySlug, 'marketing')) {
                $templateKey = 'marketing';
            } elseif (str_contains($categorySlug, 'business')) {
                $templateKey = 'business';
            }

            $templates = $lessonTemplates[$templateKey] ?? $lessonTemplates['programming'];
            $numberOfLessons = rand(3, 5);

            for ($i = 0; $i < $numberOfLessons; $i++) {
                $template = $templates[$i % count($templates)];

                Lesson::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $template['title'] . ' - ' . $course->title,
                    ],
                    [
                        'topic' => $template['topic'],
                        'title' => $template['title'],
                        'outsource_link' => $this->generateOutsourceLink($template['outsource_type']),
                        'outsource_type' => $template['outsource_type'],
                        'is_free' => $template['is_free'],
                        'duration' => $template['duration'] ?? rand(600, 3600),
                        'file_pdf' => null,
                        'order' => $i + 1,
                    ]
                );
            }
        }
    }

    /**
     * Generate a sample outsource link based on type
     */
    private function generateOutsourceLink($type): string
    {
        return match ($type) {
            'vimeo' => 'https://vimeo.com/demo/' . uniqid(),
            'firebase' => 'https://firebase.googleapis.com/v1beta/projects/demo/videos/' . uniqid(),
            'vdocipher' => 'https://dev.vdocipher.com/api/videos/' . uniqid(),
            default => 'https://youtube.com/watch?v=demo',
        };
    }
}
