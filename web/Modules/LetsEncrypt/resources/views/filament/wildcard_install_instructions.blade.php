<div>

    @if ($this->poolingInstallLog)
        <div>
            <div id="js-install-log" wire:poll="getInstallLog"
                 class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">
                {!! $this->installLog !!}
            </div>

            <script>
                window.setInterval(function() {
                    var elem = document.getElementById('js-install-log');
                    elem.scrollTop = elem.scrollHeight;
                }, 3000);
            </script>
        </div>
    @endif

    @if (isset($this->installInstructions['acmeChallangeDomain']))

        <div>

            Add the following TXT record to your DNS zone
            <br />

            <div class="bg-gray-200/50 dark:bg-white/5 rounded p-4 my-2">
            Domain: {{$this->installInstructions['acmeChallangeDomain']}} <br />
            TXT value: {{$this->installInstructions['acmeChallangeTxtValue']}}
            </div>

            <br />

            Wait for the propagation before moving to the next step <br />
            Tips: Use the following command to check the propagation
            <div class="bg-gray-200/50 dark:bg-white/5 rounded p-4 my-2">
                host -t TXT {{$this->installInstructions['acmeChallangeDomain']}}
            </div>

        </div>

    @endif
</div>
