# Подход к маршрутизации для страниц курсов

## Вариант 1: Одна страница с разделами (SPA-подход)

### Преимущества:
- Быстрая навигация между разделами без перезагрузки страницы
- Единый контекст курса
- Проще в реализации
- Лучший UX для пользователей

### Недостатки:
- Более сложная реализация прямых ссылок на разделы
- Большая начальная загрузка данных

### Реализация:

```javascript
// routes/web.php
Route::get('/courses/{slug}', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Show', [
        'course' => $course
    ]);
})->name('courses.show');
```

```vue
<!-- resources/js/pages/courses/[slug].vue -->
<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import CourseHeader from './components/CourseHeader.vue'
import CourseNavigation from './components/CourseNavigation.vue'
import CourseOverview from './components/CourseOverview.vue'
import CourseCurriculum from './components/CourseCurriculum.vue'
import CourseGallery from './components/CourseGallery.vue'
import CoursePricing from './components/CoursePricing.vue'
import CourseEnrollmentForm from './components/CourseEnrollmentForm.vue'
import CourseCallToAction from './components/CourseCallToAction.vue'

const page = usePage()
const course = computed(() => page.props.course)

// Активный раздел
const activeSection = ref('overview')

// Функция переключения разделов
const switchSection = (section) => {
  activeSection.value = section
  // Обновление URL без перезагрузки страницы
  history.pushState(null, '', `#${section}`)
}
</script>

<template>
  <div class="course-page">
    <CourseHeader :course="course" />
    <CourseNavigation 
      :active-section="activeSection" 
      @switch-section="switchSection" 
    />
    <div class="section-content">
      <CourseOverview 
        v-if="activeSection === 'overview'" 
        :course="course" 
      />
      <CourseCurriculum 
        v-if="activeSection === 'curriculum'" 
        :curriculum="course.curriculum" 
      />
      <CourseGallery 
        v-if="activeSection === 'gallery'" 
        :images="course.gallery_images" 
      />
      <CoursePricing 
        v-if="activeSection === 'pricing'" 
        :options="course.pricing_options" 
      />
      <CourseEnrollmentForm 
        v-if="activeSection === 'enroll'" 
        :config="course.enrollment_form_config" 
      />
    </div>
    <CourseCallToAction :course="course" />
  </div>
</template>
```

## Вариант 2: Отдельные страницы для каждого раздела

### Преимущества:
- Четкое разделение ответственности
- Простая маршрутизация
- Легче оптимизировать каждую страницу отдельно
- Прямые ссылки на каждый раздел

### Недостатки:
- Перезагрузка страницы при переходе между разделами
- Дублирование общих элементов (заголовка, футера)
- Более сложное управление состоянием

### Реализация:

```javascript
// routes/web.php
Route::get('/courses/{slug}', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Overview', [
        'course' => $course
    ]);
})->name('courses.overview');

Route::get('/courses/{slug}/curriculum', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Curriculum', [
        'course' => $course
    ]);
})->name('courses.curriculum');

Route::get('/courses/{slug}/gallery', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Gallery', [
        'course' => $course
    ]);
})->name('courses.gallery');

Route::get('/courses/{slug}/pricing', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Pricing', [
        'course' => $course
    ]);
})->name('courses.pricing');

Route::get('/courses/{slug}/enroll', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Enroll', [
        'course' => $course
    ]);
})->name('courses.enroll');
```

## Вариант 3: Гибридный подход (рекомендуемый)

### Преимущества:
- Комбинирует лучшие аспекты обоих подходов
- SPA для основных разделов
- Отдельные страницы для специфичных функций
- Гибкость в реализации

### Реализация:

```javascript
// routes/web.php
// Основная страница курса с разделами
Route::get('/courses/{slug}', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Show', [
        'course' => $course
    ]);
})->name('courses.show');

// Отдельная страница для формы записи (возможно с другими параметрами)
Route::get('/courses/{slug}/enroll', function ($slug) {
    $course = Course::where('slug', $slug)->firstOrFail();
    return Inertia::render('courses/Enroll', [
        'course' => $course
    ]);
})->name('courses.enroll');
```

## Рекомендация

Рекомендуется использовать **гибридный подход** со следующими особенностями:

1. **Основная страница курса** (`/courses/{slug}`) - содержит все основные разделы (обзор, программа, галерея, стоимость) с навигацией без перезагрузки страницы.

2. **Отдельная страница записи** (`/courses/{slug}/enroll`) - для формы записи, что позволяет:
   - Создать отдельную точку входа для рекламных кампаний
   - Реализовать специфичную валидацию формы
   - Отслеживать конверсии более точно

3. **Поддержка якорных ссылок** на основной странице:
   - `/courses/drawing#curriculum`
   - `/courses/drawing#gallery`
   - `/courses/drawing#pricing`

Этот подход обеспечивает оптимальный баланс между удобством навигации и технической реализацией.