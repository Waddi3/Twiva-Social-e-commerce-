<label {{ $attributes->class('w-fit flex gap-2 items-center select-none cursor-pointer')->only('class') }}>
    <input type="checkbox" class="opacity-0 absolute peer" {{ $attributes->except('class') }} />
    <div class="bg-white border-2 rounded-md border-blue-400 w-4 h-4 flex flex-shrink-0 justify-center items-center peer-checked:checkbox-checked">
        <i data-feather="check" class="hidden peer-checked:block"></i>
    </div>
    {{ $slot }}
</label>
