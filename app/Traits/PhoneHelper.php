<?php

namespace App\Traits;

trait PhoneHelper
{
    /**
     * Извлекает 10 цифр из номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return string|null - 10 цифр номера телефона или null
     */
    public function extractDigits($phone)
    {
        if (!$phone) return null;
        
        // Извлекаем только цифры
        $digits = preg_replace('/\D/', '', $phone);
        
        // Если начинается с 7 или 8, убираем первую цифру
        if (strlen($digits) === 11 && ($digits[0] === '7' || $digits[0] === '8')) {
            return substr($digits, 1);
        }
        
        // Если уже 10 цифр, возвращаем как есть
        if (strlen($digits) === 10) {
            return $digits;
        }
        
        // Если меньше 10 цифр, возвращаем null
        return null;
    }

    /**
     * Проверяет корректность номера телефона
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер корректный
     */
    public function validatePhone($phone)
    {
        if (!$phone) return false;
        
        $digits = $this->extractDigits($phone);
        if (!$digits) return false;
        
        // Проверяем, что номер начинается с допустимого кода оператора
        $validPrefixes = [
            '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'
        ];
        
        foreach ($validPrefixes as $prefix) {
            if (strpos($digits, $prefix) === 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Форматирует номер телефона для отображения
     * @param string $phone - Номер телефона в любом формате
     * @return string - Отформатированный номер телефона
     */
    public function formatPhone($phone)
    {
        if (!$phone) return '';
        
        $digits = $this->extractDigits($phone);
        if (!$digits) return $phone;
        
        // Форматируем как +7 (921) 924-52-28
        return '+7 (' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6, 2) . '-' . substr($digits, 8, 2);
    }

    /**
     * Проверяет, является ли номер действительным российским номером
     * @param string $phone - Номер телефона в любом формате
     * @return bool - true, если номер является действительным российским номером
     */
    public function isValidRussianPhone($phone)
    {
        return $this->validatePhone($phone);
    }
}