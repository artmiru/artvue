# Структура данных для новых разделов курса

## Расширение модели Course

Для реализации всех разделов курса необходимо расширить модель Course дополнительными полями:

```php
// В файле app/Models/Course.php
protected $fillable = [
    'name',
    'image',
    'alt',
    'description',
    'price',
    'slug',
    'meta_title',
    'meta_description',
    'category',
    // Новые поля:
    'curriculum',              // Программа курса
    'gallery_images',          // Галерея изображений
    'pricing_options',         // Варианты цен
    'enrollment_form_config',  // Конфигурация формы записи
    'duration',                // Продолжительность курса
    'schedule',                // Расписание занятий
    'requirements',            // Требования к студентам
    'learning_outcomes',       // Что получат студенты
];

protected $casts = [
    'price' => 'integer',
    'gallery_images' => 'array',          // Массив путей к изображениям
    'pricing_options' => 'array',         // Массив вариантов цен
    'enrollment_form_config' => 'array',  // Конфигурация формы
    'schedule' => 'array',                // Расписание занятий
    'requirements' => 'array',            // Требования
    'learning_outcomes' => 'array',       // Результаты обучения
];
```

## Структура данных для каждого раздела

### 1. Программа курса (curriculum)

```json
{
  "overview": "Общий обзор программы курса",
  "modules": [
    {
      "title": "Модуль 1: Основы рисунка",
      "duration": "4 занятия",
      "topics": [
        "Основы композиции",
        "Перспектива и пропорции",
        "Свет и тень"
      ],
      "description": "В этом модуле вы освоите фундаментальные принципы рисунка"
    },
    {
      "title": "Модуль 2: Портретное рисование",
      "duration": "6 занятий",
      "topics": [
        "Анатомия лица",
        "Передача эмоций",
        "Работа с моделью"
      ],
      "description": "Изучение особенностей рисования портрета"
    }
  ],
  "total_duration": "10 занятий"
}
```

### 2. Галерея работ (gallery_images)

```json
[
  {
    "id": 1,
    "path": "/assets/img/courses/drawing/student_work_1.jpg",
    "title": "Портрет карандашом",
    "description": "Работа студента 1 курса",
    "student_name": "Анна Петрова",
    "year": 2024
  },
  {
    "id": 2,
    "path": "/assets/img/courses/drawing/student_work_2.jpg",
    "title": "Натюрморт с фруктами",
    "description": "Работа студента 2 курса",
    "student_name": "Иван Сидоров",
    "year": 2023
  }
]
```

### 3. Стоимость (pricing_options)

```json
[
  {
    "name": "Базовый пакет",
    "price": 15000,
    "original_price": 18000,
    "discount": 17,
    "description": "Полный курс с групповыми занятиями",
    "features": [
      "10 практических занятий",
      "Материалы предоставляются",
      "Группа до 8 человек"
    ],
    "is_popular": false
  },
  {
    "name": "Премиум пакет",
    "price": 25000,
    "original_price": 30000,
    "discount": 17,
    "description": "Курс с индивидуальными занятиями",
    "features": [
      "10 индивидуальных занятий",
      "Все материалы предоставляются",
      "Персональная программа",
      "Обратная связь по email"
    ],
    "is_popular": true
  }
]
```

### 4. Форма записи (enrollment_form_config)

```json
{
  "fields": [
    {
      "name": "full_name",
      "type": "text",
      "label": "ФИО",
      "required": true,
      "placeholder": "Введите ваше полное имя"
    },
    {
      "name": "email",
      "type": "email",
      "label": "Email",
      "required": true,
      "placeholder": "your@email.com"
    },
    {
      "name": "phone",
      "type": "tel",
      "label": "Телефон",
      "required": true,
      "placeholder": "+7 (99) 99-99-99"
    },
    {
      "name": "experience",
      "type": "select",
      "label": "Опыт рисования",
      "required": false,
      "options": [
        {"value": "beginner", "label": "Нет опыта"},
        {"value": "basic", "label": "Базовые навыки"},
        {"value": "intermediate", "label": "Средний уровень"},
        {"value": "advanced", "label": "Продвинутый уровень"}
      ]
    },
    {
      "name": "package",
      "type": "radio",
      "label": "Выберите пакет",
      "required": true,
      "options": [
        {"value": "basic", "label": "Базовый пакет - 15 000 ₽"},
        {"value": "premium", "label": "Премиум пакет - 25 000 ₽"}
      ]
    },
    {
      "name": "message",
      "type": "textarea",
      "label": "Дополнительная информация",
      "required": false,
      "placeholder": "Есть ли у вас вопросы или пожелания?"
    }
  ],
  "submit_text": "Записаться на курс",
  "success_message": "Спасибо за запись! Мы свяжемся с вами в ближайшее время."
}
```

### 5. Расписание занятий (schedule)

```json
[
  {
    "day": "Понедельник",
    "time": "18:00 - 20:00",
    "location": "ул. Искусств, д. 15, каб. 301",
    "type": "group"
  },
  {
    "day": "Среда",
    "time": "19:00 - 21:00",
    "location": "ул. Искусств, д. 15, каб. 302",
    "type": "individual"
  }
]
```

### 6. Требования и результаты обучения

```json
// requirements
[
  "Наличие базовых художественных материалов (карандаши, бумага)",
  "Желание учиться и развиваться",
  "Возраст от 14 лет"
]

// learning_outcomes
[
  "Освоение основ композиции и перспективы",
  "Умение передавать свет и тень в рисунке",
  "Развитие художественного зрения",
  "Создание собственного портфолио",
  "Подготовка к дальнейшему обучению в художественной школе"
]