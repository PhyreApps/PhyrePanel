<div>
    <div id="js-install-log" wire:poll="installLog" class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">

        {!! $this->install_log !!}

    </div>

    <script>
        window.setInterval(function() {
            var elem = document.getElementById('js-install-log');
            elem.scrollTop = elem.scrollHeight;
        }, 3000);
    </script>
</div>
