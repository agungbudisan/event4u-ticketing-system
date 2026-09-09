<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-[#7B0015] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition duration-150 hover:bg-[#E15B3F] focus:outline-none focus:ring-2 focus:ring-[#E15B3F] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
