@foreach($customers as $customer)
<tr>
    <td>
        <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}">
    </td>
    <td>{{ $customer->name }}</td>
    <td>{{ $customer->email }}</td>
</tr>
@endforeach
