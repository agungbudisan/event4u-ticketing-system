@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-[#E9E1D5] bg-white shadow-sm focus:border-[#E15B3F] focus:ring-[#E15B3F] disabled:bg-[#F1ECE3]']) }}>
