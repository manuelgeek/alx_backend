<?php


namespace Tests\Unit;


use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;

class AuthControllerTest extends TestCase
{
    use CreatesApplication;

    public function testUserCreateAccount()
    {
        $request = [
            'name' => 'Test Name',
            'email' => 'mail@mail.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ];
        $result = $this->postJson('api/v1/register', $request)->json();

        $expected = [
            'message' => 'Registration Successful',
            'token' => '2|dmHjogARkl6t4p5UTQdMuipZ3Liyq6ELvaKHnwsx',
            'user' =>
                [
                    'name' => 'ALX APP',
                    'email' => 'user@mail.com',
                    'email_verified_at' => '2020-10-12T18:06:53.000000Z',
                    'updated_at' => '2020-10-12T18:06:53.000000Z',
                    'created_at' => '2020-10-12T18:06:53.000000Z',
                    'id' => 3,
                ],
        ];


        $this->assertEquals($result['message'], $expected['message']);
    }

    public function testUserRegisterValidationError()
    {

        $result = $this->postJson('api/v1/register', [])->json();

        $expected = '{
            "message": "The given data was invalid.",
            "errors": {
                "name": [
                    "The name field is required."
                ],
                "email": [
                    "The email field is required."
                ],
                "password": [
                    "The password field is required."
                ]
            }
        }';


        $this->assertJson($expected, json_encode($result));
    }

    public function testUserLogin()
    {
//        $user = Sanctum::actingAs(
//            factory(User::class)->create(),
//            ['*']
//        );
        $user = User::factory()->create();
        $request = [
            'email' => $user->email,
            'password' => 'password',
        ];
        $result = $this->postJson('api/v1/login', $request)->json();

        $expected = [
            'message' => 'Login successfull',
            'token' => '3|BB54L8Fk0IXVpkvvYExERUGGK5C6eguSnD92dHMa',
            'user' =>
                [
                    'id' => 3,
                    'name' => 'ALX APP',
                    'email' => 'user@mail.com',
                    'email_verified_at' => '2020-10-12T18:06:53.000000Z',
                    'created_at' => '2020-10-12T18:06:53.000000Z',
                    'updated_at' => '2020-10-12T18:06:53.000000Z',
                ],
        ];


        $this->assertEquals($result['message'], $expected['message']);
    }

    public function testUserLoginValidationError()
    {
        $user = User::factory()->create();
        $request = [
            'email' => $user->email,
            'password' => 'secret',
        ];

        $result = $this->postJson('api/v1/login', $request)->json();

        $expected = '{
            "message": "The given data was invalid.",
            "errors": {
                "email": [
                    "The provided credentials are incorrect."
                ]
            }
        }';


        $this->assertJson($expected, json_encode($result));
    }

}
