<h2>Invoice Reminder {{ $invoice->invoice_number }}</h2>
<p>This is a reminder for {{ $invoice->facility->name }} invoice {{ $invoice->invoice_number }}.</p>
<p><strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $invoice->status)) }}<br><strong>Total due:</strong> ${{ number_format($invoice->grand_total - $invoice->amount_paid, 2) }}<br><strong>Due date:</strong> {{ $invoice->due_date->format('M j, Y') }}</p>
<p><a href="{{ route('client.invoices.show', $invoice) }}">Pay Now</a></p>
