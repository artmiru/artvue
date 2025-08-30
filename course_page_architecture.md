# Архитектура страницы курса с несколькими разделами

## Текущая структура страницы курса (Drawing.vue)

```mermaid
graph TD
    A[Drawing.vue - Страница курса] --> B[Заголовок и описание курса]
    A --> C[Список основных тем]
    A --> D[Преимущества обучения]
    A --> E[Примеры работ студентов]
    A --> F[Призыв к действию]
```

## Предлагаемая архитектура с дополнительными разделами

```mermaid
graph TD
    A[Страница курса] --> B[Заголовок и описание курса]
    A --> C[Навигация по разделам]
    C --> C1[Обзор курса]
    C --> C2[Программа курса]
    C --> C3[Галерея работ]
    C --> C4[Стоимость]
    C --> C5[Форма записи]
    
    A --> D[Основной контент]
    D --> D1[Активный раздел]
    
    A --> E[Призыв к действию]
```

## Подробная структура компонентов

```mermaid
graph TD
    A[CoursePage.vue] --> B[CourseHeader]
    A --> C[CourseNavigation]
    A --> D[RouterView / DynamicComponent]
    A --> E[CourseCallToAction]
    
    C --> C1[NavigationItem - Обзор]
    C --> C2[NavigationItem - Программа]
    C --> C3[NavigationItem - Галерея]
    C --> C4[NavigationItem - Стоимость]
    C --> C5[NavigationItem - Запись]
    
    D --> D1[CourseOverview]
    D --> D2[CourseCurriculum]
    D --> D3[CourseGallery]
    D --> D4[CoursePricing]
    D --> D5[CourseEnrollmentForm]
```

## Альтернативный подход: Одна страница с разделами

```mermaid
graph TD
    A[CoursePage.vue] --> B[CourseHeader]
    A --> C[SectionTabs]
    A --> D[SectionContent]
    A --> E[CourseCallToAction]
    
    C --> C1[Tab - Обзор]
    C --> C2[Tab - Программа]
    C --> C3[Tab - Галерея]
    C --> C4[Tab - Стоимость]
    C --> C5[Tab - Запись]
    
    D --> D1[OverviewSection]
    D --> D2[CurriculumSection]
    D --> D3[GallerySection]
    D --> D4[PricingSection]
    D --> D5[EnrollmentFormSection]