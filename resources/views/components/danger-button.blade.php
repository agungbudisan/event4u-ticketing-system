<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-red-700 px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition duration-150 hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
