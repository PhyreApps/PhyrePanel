<div class="flex items-center justify-between mt-6 text-center">
    <div>
        <h1>
            Total Disk
        </h1>
        <div class="text-3xl">{{ $data['disk']['total'] }}</div>
    </div>
    <div>
        <h1>Used Disk</h1>
        <div class="text-3xl">{{ $data['disk']['used'] }}</div>
    </div>
    <div>
        <h1>Free Disk</h1>
        <div class="text-3xl">{{ $data['disk']['free'] }}</div>
    </div>
</div>
