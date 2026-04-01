<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tickets — {{ $order->event->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
            background: #ffffff;
            color: #111111;
        }

        .ticket {
            width: 100%;
            min-height: 100vh;
            padding: 56px 64px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .ticket+.ticket {
            page-break-before: always;
        }

        .event-header {
            border-bottom: 2px solid #111111;
            padding-bottom: 24px;
        }

        .event-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #888888;
            margin-bottom: 10px;
        }

        .event-title {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .event-meta-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #444444;
        }

        .event-meta-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            color: #888888;
        }

        .ticket-body {
            display: flex;
            gap: 56px;
            align-items: flex-start;
        }

        .ticket-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #888888;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 600;
            color: #111111;
        }

        .detail-value.reference {
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            letter-spacing: 0.05em;
        }

        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .qr-wrapper {
            width: 180px;
            height: 180px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .qr-wrapper svg {
            width: 100% !important;
            height: 100% !important;
        }

        .qr-placeholder {
            width: 180px;
            height: 180px;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
            padding: 16px;
        }

        .qr-hint {
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }

        .ticket-footer {
            margin-top: auto;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-note {
            font-size: 11px;
            color: #9ca3af;
        }

        .footer-number {
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    @foreach ($order->tickets as $ticket)
        <div class="ticket">
            <div class="event-header">
                <p class="event-label">Event Ticket</p>
                <h1 class="event-title">{{ $order->event->title }}</h1>
                <div class="event-meta">
                    <div class="event-meta-row">
                        <svg class="event-meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        {{ $order->event->starts_at->setTimezone($order->event->timezone)->format('l, F j, Y \a\t g:i A') }}
                        ({{ $order->event->timezone }})
                    </div>
                    <div class="event-meta-row">
                        <svg class="event-meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ $order->event->venue_name }}, {{ $order->event->address }}
                    </div>
                </div>
            </div>

            <div class="ticket-body">
                <div class="ticket-details">
                    <div class="detail-item">
                        <span class="detail-label">Ticket Type</span>
                        <span class="detail-value">{{ $ticket->ticketType->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Attendee</span>
                        <span class="detail-value">{{ $ticket->attendee_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking Reference</span>
                        <span class="detail-value reference">{{ $ticket->booking_reference }}</span>
                    </div>
                </div>

                <div class="qr-section">
                    @if ($svgContents[$ticket->uuid] ?? null)
                        <div class="qr-wrapper">
                            {!! $svgContents[$ticket->uuid] !!}
                        </div>
                        <span class="qr-hint">Scan at the door</span>
                    @else
                        <div class="qr-placeholder">QR code not available</div>
                    @endif
                </div>
            </div>

            <div class="ticket-footer">
                <span class="footer-note">Present this ticket at the entrance.</span>
                <span class="footer-number">{{ $loop->iteration }} / {{ $loop->count }}</span>
            </div>
        </div>
    @endforeach
</body>

</html>
