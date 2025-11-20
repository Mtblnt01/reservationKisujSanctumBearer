<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attribute;


class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    
    use RefreshDatabase;
    #[Test]
    public function user_can_register(){
        //Arrange
        $payload = [
            "name" => "pumpal",
            "email" => "pumpal@moriczref.hu",
            "password" => "jelszo_2025",
            "password_confirmation" => "jelszo_2025",
        ];
        //Act
        $response = $this->postJson('/api/register', $payload);
        //Assert
        $response->assertStatus(201)->assertJsonStructure(['message', 'user']);
        $this->assertDatabaseHas('users', ['email' => 'pumpal@moriczref.hu']);
    }

    #[Test]
    public function user_can_login_and_receive_token(){
        //Arrange
        $user = User::factory()->create([
            'email' => "teszt@example.com",
            'password' => bcrypt('password'),
        ]);

        $credentials = [
            'email' => "teszt@example.com",
            'password' => 'password',
        ];
        //Act

        $response = $this->postJson('/api/login', $credentials);
        //Assert
        $response->assertStatus(200)->assertJsonStructure(['access_token', 'token_type']);
    }
    #[Test]
    public function user_can_logout(){
        //Arrange
        //Act
        //Assert
    }
}
