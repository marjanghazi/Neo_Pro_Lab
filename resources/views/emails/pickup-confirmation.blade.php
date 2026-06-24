<x-mail::message>
# Pickup Request Received

Dear {{ $data['name'] }},

Thank you for scheduling a pickup with NeoProLab Couriers.

**Request Details:**
- Request Number: {{ $data['requestNumber'] }}
- Scheduled Date: {{ $data['pickupDate'] }}
- Preferred Time: {{ $data['pickupTime'] }}

Our team will contact you within 2 hours to confirm the pickup details. If you have any urgent questions, please call (508) 933-6750.

<x-mail::button :url="route('home')">
Visit Our Website
</x-mail::button>

Best regards,<br>
The NeoProLab Team
</x-mail::message>