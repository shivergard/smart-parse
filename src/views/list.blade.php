@foreach ($list as $item)
<tr>
    @foreach($fields as $col)
        @if ($col == 'table_url')
             <td><a href="{{ action('\Shivergard\SmartParse\SmartParseController@singleTable' , array('name' => $item->name))}}">Parse Table</a></td>
        @else
            <td>{{ $item->$col }}</td>
        @endif
    @endforeach
</tr>
@endforeach