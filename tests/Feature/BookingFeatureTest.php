<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ScheduledOff;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookingFeatureTest extends TestCase
{
    use DatabaseTransactions;

    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --seed');
    }

    /**
     * Test the getSlots method.
     *
     * @return void
     */
    public function testGetSlots()
    {

        $service = Service::first();

        // get a scheduled off for the service
        $scheduledOff = ScheduledOff::where('service_id',$service->id)->first();

        $response = $this->getJson('/api/get-slots');

        // Check that the response is successful
        $response->assertStatus(Response::HTTP_OK);

        // Check that the service is in the response
        $response->assertJsonFragment([
            'id' => $service->id,
            'service' => $service->name,
        ]);

        // Check that the slot is not in the response
        $response->assertJsonMissing([
            'date' => date('Y-m-d', strtotime($scheduledOff->start_time)),
        ]);
    }

    /**
     * Test the saveSlot method.
     *
     * @return void
     */
    public function testSaveSlot()
    {
        $service = Service::first();

        // Create a booking
        $booking = Booking::factory()->create([
            'service_id' => $service->id,
        ]);

        // Generate booking data
        $bookingData = [
            'service_id' => $service->id,
            'booking_date' => date('Y-m-d',strtotime($booking->start_time)),
            'start_time' => date('H:i',strtotime($booking->start_time)),
            'end_time' => date('H:i',strtotime($booking->end_time)),
            'first_name' => [$this->faker->firstName, $this->faker->firstName],
            'last_name' => [$this->faker->lastName, $this->faker->lastName],
            'email' => [$this->faker->email, $this->faker->email],
        ];

        // Call the API endpoint
        $response = $this->postJson('/api/save-slots', $bookingData);

        // Check that the response is successful
        $response->assertStatus(Response::HTTP_OK);

        // Check that the bookings have been created
        $this->assertDatabaseCount('bookings', 3);
    }


}
