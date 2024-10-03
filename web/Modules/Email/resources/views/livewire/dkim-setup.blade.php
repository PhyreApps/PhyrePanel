<div>
<div>
    @if(isset($secure['text']))

       <div class="w-full p-2">
           Please add the following DNS record to your domain's DNS settings:
       </div>

         <div class="w-full mt-2">
             <textarea rows="10" class="w-full p-4 rounded-md border-2 border-gray-100">
                 {{ $secure['text'] }}
        </textarea>
         </div>

        @if($verify && !empty($verify))
            <div>
                <div class="w-full p-2">
                    Verification Results:
                    @if ($verify['pass'])
                        <span class="text-green">Pass</span>
                    @else
                        <span class="text-red">Fail</span>
                    @endif
                </div>
            </div>
            @foreach($verify['checks'] as $v)
                @php
                    if ($v['pass']) {
                        continue;
                    }
                @endphp
                <div class="w-full p-2">
                   Check: {{ $v['check'] }} <br />
                    Pass: {{ $v['pass'] ? 'Yes' : 'No' }} <br />
                    Result: {{ $v['result'] }} <br />
                    Return result must be: {{ $v['must'] }}
                </div>
            @endforeach
        @endif

    <div>
        Please add DMARC TXT record to your domain _dmarc.{{$mainDomain}} with the following value:
        <br />
        v=DMARC1; p=none
    </div>

    <div>
       Please add SPF record to your domain {{$mainDomain}}:
        <br />
        v=spf1 a mx ip4:{{$serverIp}} ~all
    </div>

        <x-filament::button wire:click="verify">
            Check DNS Record
        </x-filament::button>

    @endif
</div>
</div>
