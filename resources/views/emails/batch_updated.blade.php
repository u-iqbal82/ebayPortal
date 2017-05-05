<p>Batch status updated notification.</p>

<p>Below are the details:</p>

<p>Batch Name : {{ $batch->name }}<br />
Total Articles : {{ count($batch->articles) }}<br />
Batch Status : {{ $batch->status }}</p>

<p>Batch Updated by : {{ $batch->name }}</p>