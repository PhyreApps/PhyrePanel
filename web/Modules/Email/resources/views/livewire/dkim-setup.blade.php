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

        <x-filament::button wire:click="verify">
            Check DNS Record
        </x-filament::button>

    @endif
</div>
