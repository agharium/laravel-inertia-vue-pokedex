export type PokemonSummary = {
    id: number;
    name: string;
    sprite_url: string | null;
    is_favorite: boolean;
};

export type PokemonStat = {
    name: string;
    base_stat: number;
};

export type PokemonDetail = {
    id: number;
    name: string;
    sprite_url: string | null;
    types: string[];
    height: number;
    weight: number;
    abilities: string[];
    stats: PokemonStat[];
    is_favorite: boolean;
};

export type PokemonListMeta = {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number | null;
    to: number | null;
};

export type PokemonListLinks = {
    prev: string | null;
    next: string | null;
};

export type PokemonListPayload = {
    data: PokemonSummary[];
    meta: PokemonListMeta;
    links: PokemonListLinks;
};
