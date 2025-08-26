<?php

namespace App\Console\Commands;

use App\Models\ScheduleTemplate;
use App\Models\ScheduleEvent;
use App\Models\MasterClass;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSchedule extends Command
{
    protected $signature = 'schedule:generate';
    protected $description = 'Generate schedule for the next 3 months (regular and master classes)';

    public function handle()
    {
        $now = Carbon::now();

        $endDate = $now->copy()->addMonths(3); // 3 месяца вперед для регулярных уроков
        $startDate = $now->copy();

        $this->info("Current time: {$now->format('Y-m-d H:i:s')}");
        $this->info("Start date: {$startDate->format('Y-m-d H:i:s')}");
        $this->info("End date: {$endDate->format('Y-m-d H:i:s')}");

        $this->generateForPeriod($startDate->copy(), $endDate->copy());

        // Для мастер-классов только на месяц вперед
        $masterClassEndDate = $now->copy()->addMonth();
        $this->generateMasterClasses($startDate->copy(), $masterClassEndDate);

        $this->info("Regular schedule generated until {$endDate->format('Y-m-d')}");
        $this->info("Master classes schedule generated until {$masterClassEndDate->format('Y-m-d')}");
        $this->info("Schedule generation completed at {$now->format('Y-m-d H:i:s')}");
    }

    private function generateForPeriod(Carbon $start, Carbon $end)
    {
        $templates = ScheduleTemplate::where('is_active', true)->get();

        while ($start->lte($end)) {
            foreach ($templates as $template) {
                if ($start->dayOfWeek == $template->day_of_week) {
                    $time = is_string($template->start_time)
                        ? $template->start_time
                        : (string) $template->start_time;
                    $datetime = $start->copy()->setTimeFrom(
                        Carbon::parse($time)
                    );

                    ScheduleEvent::firstOrCreate([
                        'start_datetime' => $datetime,
                        'teacher_id' => $template->teacher_id
                    ], [
                        'is_active' => true
                    ]);
                }
            }
            $start->addDay();
        }
    }

    private function generateMasterClasses(Carbon $start, Carbon $end): void
    {
        // Формируем шаблоны для МК: Пн 19:00, Сб 19:00, Вс 15:00
        $slots = [
            ['dow' => Carbon::MONDAY, 'time' => '19:00:00', 'teacher_id' => 2],
            ['dow' => Carbon::SATURDAY, 'time' => '19:00:00', 'teacher_id' => 2],
            ['dow' => Carbon::SUNDAY, 'time' => '15:00:00', 'teacher_id' => 2],
        ];

        // Получаем активные мастер-классы с их фото
        $masterClasses = MasterClass::query()
            ->where('is_active', true)
            ->whereNotNull('image_path')
            ->select('id', 'image_path')
            ->get();

        $this->info("Found " . $masterClasses->count() . " active master classes with images for scheduling.");

        if ($masterClasses->isEmpty()) {
            $this->warn('No active master classes with images found to scheduling.');
            return;
        }

        // Группируем мастер-классы по уникальным фото
        $uniqueImages = $masterClasses->groupBy('image_path');
        $this->info("Found " . $uniqueImages->count() . " unique images.");

        $createdCount = 0;
        $this->info("Generating events from {$start->format('Y-m-d')} to {$end->format('Y-m-d')}");

        // Генерируем события только для указанных слотов на месяц вперед
        while ($start->lte($end)) {
            foreach ($slots as $slot) {
                if ($start->dayOfWeek === $slot['dow']) {
                    $datetime = $start->copy()->setTimeFrom(Carbon::parse($slot['time']));

                    // Проверяем, что дата в будущем
                    if ($datetime->isPast()) {
                        $this->line("Skipping past date: {$datetime->format('Y-m-d H:i')}");
                        continue;
                    }

                    // Проверяем, есть ли уже событие для этого времени
                    $existingEvent = ScheduleEvent::where('start_datetime', $datetime)
                        ->whereNotNull('master_class_id')
                        ->first();

                    if ($existingEvent) {
                        $this->line("Event already exists for {$datetime->format('Y-m-d H:i')}");
                        continue;
                    }

                    // Выбираем мастер-класс с уникальным фото, если возможно
                    $masterClassId = $this->selectMasterClassWithUniqueImage($uniqueImages);

                    try {
                        $event = ScheduleEvent::create([
                            'start_datetime' => $datetime,
                            'master_class_id' => $masterClassId,
                            'teacher_id' => $slot['teacher_id'], // Добавляем преподавателя из слота
                            'is_active' => true,
                            'max_participants' => 8,
                        ]);
                        $createdCount++;
                        $this->line("Created event for {$datetime->format('Y-m-d H:i')} with master_class_id {$masterClassId}");
                    } catch (\Exception $e) {
                        $this->error("Failed to create event for {$datetime->format('Y-m-d H:i')}: " . $e->getMessage());
                    }
                }
            }
            $start->addDay();
        }

        $this->info("Created {$createdCount} new master class schedule events.");
    }

    /**
     * Выбирает мастер-класс с уникальным фото, если возможно
     */
    private function selectMasterClassWithUniqueImage($uniqueImages): int
    {
        // Если есть неиспользованные уникальные фото, выбираем из них
        foreach ($uniqueImages as $imagePath => $masterClasses) {
            if ($masterClasses->isNotEmpty()) {
                $masterClass = $masterClasses->shift(); // Берем и удаляем из группы
                return $masterClass->id;
            }
        }

        // Если все уникальные фото использованы, перемешиваем и начинаем заново
        $this->line("All unique images used, reshuffling...");
        foreach ($uniqueImages as $imagePath => $masterClasses) {
            $masterClasses->push(...$masterClasses->all()); // Восстанавливаем все мастер-классы
        }

        // Выбираем случайный из любой группы
        $randomGroup = $uniqueImages->random();
        return $randomGroup->random()->id;
    }
}
