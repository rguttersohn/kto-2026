@php
    
$state = $getState();

@endphp

<ul class="i-ta-text-list-limited fi-ta-text-has-line-breaks fi-ta-text" >
    
    @foreach ($state as $key=>$value) 
        <li class="fi-size-sm  fi-ta-text-item"> {{$key}}: {{$value}}</li>
    @endforeach

</ul>