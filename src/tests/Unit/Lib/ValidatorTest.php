<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use Lib\Validator;

class ValidatorTest extends TestCase
{
    public function test_validate_json_required_and_optional(): void
    {
        // Your Validator::validateJson expects JSON string and returns [] on success or errors array
        $payload = json_encode(['name'=>'Alice','email'=>'alice@example.com','age'=>30]);
        $rules = [
            'name'  => 'required|string_length:1:50',
            'email' => 'required|email'
        ];
        $result = Validator::validateJson($payload, $rules);
        $this->assertSame([], $result, print_r($result, true));
    }

    public function test_validate_json_missing_required_should_fail(): void
    {
        $payload = json_encode(['email'=>'alice@example.com']);
        $rules   = ['name'=>'required|string_length:1:50','email'=>'required|email'];
        $result = Validator::validateJson($payload, $rules);
        $this->assertIsArray($result);
        $this->assertNotSame([], $result);
    }

    public function test_validate_at_least_one_value(): void
    {
        $ok = Validator::validateAtLeastOneValue(['a'=>'','b'=>'x'], ['a','b']);
        $this->assertFalse((bool) $ok);

        $bad = Validator::validateAtLeastOneValue(['a'=>'','b'=>''], ['a','b']);
        $this->assertTrue((bool) $bad);
    }

    public function test_validate_exact_keys_values_and_no_extra(): void
    {
        $payload = ['role'=>'admin','env'=>'prod'];
        $allowed = ['role'=>['user','manager','admin'],'env'=>['dev','stage','prod']];

        $ok = Validator::validateExactKeysValues($payload, $allowed);
        $this->assertFalse((bool) $ok);

        $bad = Validator::validateNoExtraValues(['role'=>'admin','x'=>1], array_keys($allowed));
        $this->assertTrue((bool) $bad);
    }
}
