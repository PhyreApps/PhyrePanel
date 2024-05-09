<div>
    <div id="js-backup-log" wire:poll="pullBackupLog" class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">

        {!! $this->backupLog !!}

    </div>

    <script>
        window.setInterval(function() {
            var elem = document.getElementById('js-backup-log');
            elem.scrollTop = elem.scrollHeight;
        }, 3000);
    </script>
</div>
