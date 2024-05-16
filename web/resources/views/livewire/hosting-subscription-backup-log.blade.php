<div>
    <div id="js-backup-log" wire:poll.750ms="pullBackupLog" class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">

        @if($this->backupLog == '')
            <div class="text-center text-gray-500 dark:text-gray-400">
                {{ __('No backup log available.') }}
            </div>
        @else
            {!! $this->backupLog !!}
        @endif

    </div>

    <script>
        window.setInterval(function() {
            var elem = document.getElementById('js-backup-log');
            elem.scrollTop = elem.scrollHeight;
        }, 3000);
    </script>
</div>
