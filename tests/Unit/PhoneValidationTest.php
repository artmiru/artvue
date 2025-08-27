<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\PhoneHelper;

class PhoneValidationTest extends TestCase
{
    public function test_extract_digits_from_phone()
    {
        $this->assertEquals('9219245228', extractDigits('+79219245228'));
        $this->assertEquals('9219245228', extractDigits('89219245228'));
        $this->assertEquals('9219245228', extractDigits('9219245228'));
        $this->assertEquals('9219245228', extractDigits('+7(921)924-52-28'));
        $this->assertEquals('9219245228', extractDigits('8(921)924-52-28'));
        $this->assertNull(extractDigits('invalid'));
        $this->assertNull(extractDigits('123'));
        $this->assertNull(extractDigits(''));
    }

    public function test_validate_phone()
    {
        $this->assertTrue(validatePhone('+79219245228'));
        $this->assertTrue(validatePhone('89219245228'));
        $this->assertTrue(validatePhone('9219245228'));
        $this->assertTrue(validatePhone('+7(921)924-52-28'));
        
        // Некорректные номера
        $this->assertFalse(validatePhone('invalid'));
        $this->assertFalse(validatePhone('1234567890')); // Некорректный код оператора
        $this->assertFalse(validatePhone(''));
        $this->assertFalse(validatePhone('921924522')); // Мало цифр
        $this->assertFalse(validatePhone('92192452289')); // Много цифр
    }

    public function test_format_phone()
    {
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('+79219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('89219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('9219245228'));
        $this->assertEquals('+7 (921) 924-52-28', formatPhone('+7(921)924-52-28'));
        
        // Некорректные номера
        $this->assertEquals('invalid', formatPhone('invalid'));
        $this->assertEquals('', formatPhone(''));
    }

    public function test_is_valid_russian_phone()
    {
        $this->assertTrue(isValidRussianPhone('+79219245228'));
        $this->assertTrue(isValidRussianPhone('89219245228'));
        $this->assertTrue(isValidRussianPhone('9219245228'));
        
        // Некорректные номера
        $this->assertFalse(isValidRussianPhone('invalid'));
        $this->assertFalse(isValidRussianPhone('1234567890'));
        $this->assertFalse(isValidRussianPhone(''));
    }
}