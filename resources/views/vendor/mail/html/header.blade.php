<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{asset('assets/2local_symbol_circle.svg')}}" class="logo" alt="2local">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
