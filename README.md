# Tesztek írása

-php artisan make:test AuthControllerTest
-php artisan make:test ReservationAccessTest
-php artisan make:test ReservationControllerTest


## tests/Feature/AuthControllerTest.php szerkesztése

### User tud e regisztrálni test

    <?php
    
    namespace Tests\Feature;
    
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Tests\TestCase;
    use PHPUnit\Framework\Attributes\Test;
    
    class AuthControllerTest extends TestCase
    {
        use RefreshDatabase;
    
        #[Test]
        public function user_can_register(){
            // Arrange
            $payload = [
                'name'=> 'mozso',
                'email'=> 'mozso@moriczref.hu',
                'password' => 'Jelszo_2025',
                'password_confirmation' => 'Jelszo_2025',
            ];
            // Act
            $response = $this->postJson('/api/register', $payload);
            // Assert
            $response->assertStatus(201)->assertJsonStructure(['message','user']);
            $this->assertDatabaseHas('users',['email' => 'mozso@moriczref.hu']);
        }
    }

### User tud e kijelentkezni teszt


    #[Test]
    public function user_can_logout(){
        //Arrange
            $user = User::factory()->create();
            $token = $user-createToken('auth_token')->plainTextToken;
        //Act
            $response = $this->withHeader('Authorization', 'Bearer '.$token)->postJson('api/logout');
        //Assert
            $response->assertStatus(200)->assertJson(['message' => 'User kijelentkezése sikerült!']);
    }




‎## tests/Feature/ReservationAccessTest.php szerkesztése



### megnézzük hogy az admin megtudja e nézni az összes foglalás-t
    <?php
    
    namespace Tests\Feature;
    
    use App\Models\User;
    use App\Models\Reservation;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;
    use PHPUnit\Framework\Attributes\Test;
    
    class ReservationAccessTest extends TestCase
    {
        use RefreshDatabase;
    
        #[Test]
        public function admin_can_view_all_reservations()
        {
            $admin = User::factory()->create(['is_admin' => true]);
            $user = User::factory()->create();
            $reservation = Reservation::factory()->create();
    
            $response = $this->actingAs($admin)->getJson('/api/reservations');
    
            $response->assertStatus(200)
                     ->assertJsonFragment(['id' => $reservation->id]);
        }


### megnézzük hogy a felhasználó megtudja e nézni a saját foglalásait
     #[Test]
        public function user_can_view_own_reservations()
        {
            $user = User::factory()->create();
            $reservation = Reservation::factory()->for($user)->create();
    
            $response = $this->actingAs($user)->getJson('/api/reservations');
    
            $response->assertStatus(200)
                     ->assertJsonFragment(['id' => $reservation->id]);
        }


### Megnézzük hogy egy felhasználó ne tudja megnézni a többi felhasználó foglalásait

    
    #[Test]
    public function user_cannot_view_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }


### Megnézzük hogy a felhasználó tudja a szerkeszteni a saját foglalásait

    #[Test]
    public function user_can_update_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $updateData = ['note' => 'Frissített megjegyzés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['note' => 'Frissített megjegyzés']);
    }


### Megnézzük hogy a felhasználó más foglalását NE tudja szerkeszteni

    #[Test]
    public function user_cannot_update_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $updateData = ['note' => 'Tiltott frissítés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(403);
    }

### Megnézzük hogy a felhasznaló kitudja e törölni a saját foglalásait

    #[Test]
    public function user_can_delete_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Foglalás törölve.']);
    }

### Lekell ellenőrizni hogy a felhasználó netudja más felhasználó foglalását törölni.

    #[Test]
        public function user_cannot_delete_others_reservation()
        {
            $user = User::factory()->create();
            $otherUser = User::factory()->create();
            $reservation = Reservation::factory()->for($otherUser)->create();
    
            $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");
    
            $response->assertStatus(403);
        }
    }



## ‎tests/Feature/ReservationControllerTest.php szerkesztése

### Megnézzük hogy a felhasználó tud e létrehozni foglalásokat

    <?php

    namespace Tests\Feature;
    
    use App\Models\User;
    use App\Models\Reservation;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;
    use PHPUnit\Framework\Attributes\Test;
    use Illuminate\Foundation\Testing\WithFaker;
    
    class ReservationControllerTest extends TestCase
    {
        use RefreshDatabase, WithFaker;
    
        #[Test]
        public function user_can_create_reservation()
        {
            $user = User::factory()->create();
            $payload = [
                'reservation_time' => now()->addDays(3)->toDateTimeString(),
                'guests' => 4,
                'note' => 'Teszt foglalás',
            ];
    
            $response = $this->actingAs($user)->postJson('/api/reservations', $payload);
    
            $response->assertStatus(201)
                     ->assertJsonFragment(['note' => 'Teszt foglalás']);
            $this->assertDatabaseHas('reservations', ['note' => 'Teszt foglalás']);
        }


### Megnézzük hogy a felhasználó tudja e a saját foglalásait megnézni

     #[Test]
    public function user_can_view_own_reservations()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $reservation->id]);
    }


### Le ellenőrizzük hogy a felhasználó nemtudja e más foglalását megnézni

        #[Test]
    public function user_cannot_view_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->getJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

### Megnézzük hogy az admin megtudja e más felhasználók foglalását nézni

        #[Test]
    public function admin_can_view_all_reservations()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($admin)->getJson('/api/reservations');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $reservation->id]);
    }

### Megnézzük hogy a felhasználó tudja e szerkeszteni a saját foglalását

    #[Test]
    public function user_can_update_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $updateData = ['note' => 'Frissített megjegyzés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['note' => 'Frissített megjegyzés']);
        $this->assertDatabaseHas('reservations', ['note' => 'Frissített megjegyzés']);
    }

### Ellenőrizzük hogy a felhasználó nemtudja más foglalását szerkeszteni

        #[Test]
    public function user_cannot_update_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $updateData = ['note' => 'Tiltott frissítés'];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(403);
    }

### User tudja e sajat foglalasat torolni

        #[Test]
    public function user_can_delete_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Foglalás törölve.']);
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

### User nemtud mas foglalast törölni

     #[Test]
    public function user_cannot_delete_others_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservation->id}");

            $response->assertStatus(403);
        }
    }

