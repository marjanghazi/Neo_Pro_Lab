<h2>Invoice {{ $invoice->invoice_number }}</h2>
<p>Hello {{ $invoice->facility->name }},</p>
<p>Your combined delivery invoice for {{ $invoice->period_start->format('M j, Y') }} – {{ $invoice->period_end->format('M j, Y') }} is ready.</p>
<p><strong>Total due:</strong> ${{ number_format($invoice->grand_total, 2) }}<br><strong>Due date:</strong> {{ $invoice->due_date->format('M j, Y') }}</p>
<p><a href="{{ route('client.invoices.show', $invoice) }}">View invoice and Pay Now</a></p>
