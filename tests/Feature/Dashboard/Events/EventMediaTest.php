<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\CreatesEvents;
use Tests\Traits\CreatesUsers;

uses(CreatesUsers::class, CreatesEvents::class);

it('allows admins to upload media to an event', function (): void {
    Storage::fake('local');

    $event = $this->createDraftEvent();

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.media.store', $event), [
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ])
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($event->media()->count())->toBe(1);
});

it('blocks upload when the event already has 10 media files', function (): void {
    Storage::fake('local');

    $event = $this->createDraftEvent();

    for ($i = 0; $i < 10; $i++) {
        $event->media()->create([
            'disk' => 'local',
            'path' => sprintf('events/file_%d.jpg', $i),
            'filename' => sprintf('file_%d.jpg', $i),
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'is_cover' => false,
            'sort_order' => $i,
        ]);
    }

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.media.store', $event), [
            'file' => UploadedFile::fake()->image('extra.jpg'),
        ])
        ->assertRedirect()
        ->assertSessionHas('toast_error');
});

it('allows admins to delete a media file', function (): void {
    Storage::fake('local');

    $event = $this->createDraftEvent();
    $media = $this->attachCoverImage($event);

    $this->actingAs($this->createAdmin())
        ->delete(route('dashboard.events.media.destroy', [$event, $media->uuid]))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('allows admins to set a media file as the cover image', function (): void {
    $event = $this->createDraftEvent();

    $firstMedia = $event->media()->create([
        'disk' => 'local', 'path' => 'a.jpg', 'filename' => 'a.jpg',
        'mime_type' => 'image/jpeg', 'size' => 100, 'is_cover' => true, 'sort_order' => 0,
    ]);

    $secondMedia = $event->media()->create([
        'disk' => 'local', 'path' => 'b.jpg', 'filename' => 'b.jpg',
        'mime_type' => 'image/jpeg', 'size' => 100, 'is_cover' => false, 'sort_order' => 1,
    ]);

    $this->actingAs($this->createAdmin())
        ->post(route('dashboard.events.media.cover', [$event, $secondMedia->uuid]))
        ->assertRedirect()
        ->assertSessionHas('toast_success');

    expect($secondMedia->fresh()->is_cover)->toBeTrue()
        ->and($firstMedia->fresh()->is_cover)->toBeFalse();
});

it('forbids organization admins from uploading media to another organizations event', function (): void {
    Storage::fake('local');

    $event = $this->createDraftEvent();

    $this->actingAs($this->createOrganizationAdmin())
        ->post(route('dashboard.events.media.store', $event), [
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ])
        ->assertForbidden();
});
