<div class="flex items-center justify-between mt-6 text-center">

    <div>
        <h1>
            Total Memory
        </h1>
        <div class="text-3xl">{{ $data['memory']['total'] }}</div>
    </div>

    <div>
        <h1>
            Used Memory
        </h1>
        <div class="text-3xl">{{ $data['memory']['used'] }}</div>
    </div>

    <div>
        <h1>
            Available Memory
        </h1>
        <div class="text-3xl">{{ $data['memory']['available'] }}</div>
    </div>
</div>
