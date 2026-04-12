<?php

declare(strict_types=1);

use App\Actions\GenerateTicketQrCodesAction;
use App\Actions\RecordActivityAction;
use App\Enums\ActivityEvent;
use App\Jobs\GenerateTicketQrCodesJob;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesOrders;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class, CreatesOrders::class);

it('generates a QR code SVG file for each ticket in the order', function (): void {
    Storage::fake('local');

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new GenerateTicketQrCodesJob($order))->handle(resolve(GenerateTicketQrCodesAction::class));

    $ticket = $order->tickets()->first();

    Storage::disk('local')->assertExists(sprintf('tickets/%s/%s.svg', $order->uuid, $ticket->uuid));
});

it('updates the qr_code_path on each ticket after generation', function (): void {
    Storage::fake('local');

    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    (new GenerateTicketQrCodesJob($order))->handle(resolve(GenerateTicketQrCodesAction::class));

    $ticket = $order->tickets()->first();

    expect($ticket->fresh()->qr_code_path)->not->toBeNull();
});

it('records a QrCodeGenerationFailed activity when the job fails', function (): void {
    $user = $this->createUser();
    [$event, $ticketType] = $this->createPublishedEventWithTicketType();
    $order = $this->createPaidOrder($user, $event, $ticketType);

    $job = new GenerateTicketQrCodesJob($order);
    $job->failed(new RuntimeException('Disk write error'), resolve(RecordActivityAction::class));

    $this->assertDatabaseHas('activity_logs', ['event' => ActivityEvent::QrCodeGenerationFailed->value]);
});

it('has tries set to 3', function (): void {
    $job = new GenerateTicketQrCodesJob(Order::factory()->make());

    expect($job->tries)->toBe(3);
});

it('has deleteWhenMissingModels set to true', function (): void {
    $job = new GenerateTicketQrCodesJob(Order::factory()->make());

    expect($job->deleteWhenMissingModels)->toBeTrue();
});
