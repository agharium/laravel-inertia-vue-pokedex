const TYPE_STYLES: Record<string, string> = {
    normal: 'bg-stone-400 text-white',
    fire: 'bg-orange-500 text-white',
    water: 'bg-sky-500 text-white',
    electric: 'bg-amber-400 text-stone-900',
    grass: 'bg-emerald-500 text-white',
    ice: 'bg-cyan-300 text-stone-900',
    fighting: 'bg-red-700 text-white',
    poison: 'bg-fuchsia-600 text-white',
    ground: 'bg-yellow-700 text-white',
    flying: 'bg-indigo-300 text-stone-900',
    psychic: 'bg-pink-500 text-white',
    bug: 'bg-lime-600 text-white',
    rock: 'bg-yellow-800 text-white',
    ghost: 'bg-violet-700 text-white',
    dragon: 'bg-indigo-700 text-white',
    dark: 'bg-stone-700 text-white',
    steel: 'bg-slate-400 text-stone-900',
    fairy: 'bg-pink-300 text-stone-900',
};

export function typeBadgeClass(type: string): string {
    return TYPE_STYLES[type] ?? 'bg-slate-500 text-white';
}

export function formatPokemonName(name: string): string {
    return name
        .split('-')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

export function formatStatName(name: string): string {
    const labels: Record<string, string> = {
        hp: 'HP',
        attack: 'Attack',
        defense: 'Defense',
        'special-attack': 'Sp. Atk',
        'special-defense': 'Sp. Def',
        speed: 'Speed',
    };

    return labels[name] ?? formatPokemonName(name);
}
