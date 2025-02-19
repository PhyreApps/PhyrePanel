<div>

    @php
    $args = $this->error->getArguments();
    @endphp

    @if(isset($args['message']))

       <h4 class="text-red-500">
           {{ $args['message'] }}
       </h4>

    @endif

</div>
