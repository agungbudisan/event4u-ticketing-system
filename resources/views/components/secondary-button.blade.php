<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-full border border-[#E9E1D5] bg-white px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#211F1C] shadow-sm transition duration-150 hover:border-[#E15B3F] hover:bg-[#F8F4EC] focus:outline-none focus:ring-2 focus:ring-[#E15B3F] focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
