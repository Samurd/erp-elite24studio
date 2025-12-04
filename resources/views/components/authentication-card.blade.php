<div
    class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-black via-yellow-600 to-yellow-400">
    <div>

        {{ $title ?? '' }}
        {{-- {{ $logo }} --}}
    </div>

    <div
        class="w-full sm:max-w-md mt-6 px-6 py-4 h-[400px] flex flex-col justify-center items-center  bg-black bg-opacity-70 backdrop-blur-sm text-white overflow-hidden sm:rounded-xl">
        {{ $slot }}
    </div>
</div>