/**
 * Извлекает 10 цифр из номера телефона
 * @param {string} phone - Номер телефона в любом формате
 * @returns {string|null} - 10 цифр номера телефона или null
 */
export function extractDigits(phone) {
    if (!phone) return null;
    
    // Извлекаем только цифры
    const digits = phone.replace(/\D/g, '');
    
    // Если начинается с 7 или 8, убираем первую цифру
    if (digits.length === 11 && (digits[0] === '7' || digits[0] === '8')) {
        return digits.substring(1);
    }
    
    // Если уже 10 цифр, возвращаем как есть
    if (digits.length === 10) {
        return digits;
    }
    
    // Если меньше 10 цифр, возвращаем null
    return null;
}

/**
 * Проверяет корректность номера телефона
 * @param {string} phone - Номер телефона в любом формате
 * @returns {boolean} - true, если номер корректный
 */
export function validatePhone(phone) {
    if (!phone) return false;
    
    const digits = extractDigits(phone);
    if (!digits) return false;
    
    // Проверяем, что номер начинается с допустимого кода оператора
    const validPrefixes = [
        '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'
    ];
    
    return validPrefixes.some(prefix => digits.startsWith(prefix));
}

/**
 * Форматирует номер телефона для отображения
 * @param {string} phone - Номер телефона в любом формате
 * @returns {string} - Отформатированный номер телефона
 */
export function formatPhone(phone) {
    if (!phone) return '';
    
    const digits = extractDigits(phone);
    if (!digits) return phone;
    
    // Форматируем как +7 (921) 924-52-28
    return `+7 (${digits.substring(0, 3)}) ${digits.substring(3, 6)}-${digits.substring(6, 8)}-${digits.substring(8, 10)}`;
}

/**
 * Проверяет, является ли номер действительным российским номером
 * @param {string} phone - Номер телефона в любом формате
 * @returns {boolean} - true, если номер является действительным российским номером
 */
export function isValidRussianPhone(phone) {
    return validatePhone(phone);
}