import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface Analysis {
    id?: number;
    numero_rapport: string;
    lieu_prelevement: string;
    date_heure_reception_laboratoire: string;
    conditions_conservation: string;
    date_heure_analyse: string;
    fournisseur_fabricant: string;
    conditionnement: string;
    agrement: string;
    lot: string;
    type_peche: string;
    nom_produit: string;
    espece: string;
    origine: string;
    date_emballage: string;
    date_consommation: string;
    imp: string;
    hx: string;
    note_nucleotide: string;
    cotation_fraicheur: string;
    observations: string;
    ref_rapport: string;
    created_at?: string;
    updated_at?: string;
    [key: string]: unknown; // For any additional properties
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}
