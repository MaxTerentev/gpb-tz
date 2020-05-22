<ul>
    @foreach($tree as $node)
        <li>{{ $node['position'] }} {{ $node['title'] }} ({{ number_format($node['value'], 2) }} {{ $currency }})</li>
        @if(isset($node['childs']))
            <li>
                @include('tree.widget', ['tree' => $node['childs']])
            </li>
        @endif
    @endforeach
</ul>