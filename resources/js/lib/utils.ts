import type { LinkComponentBaseProps } from '@inertiajs/core';
import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

const indonesiaCurrencyFormatter = new Intl.NumberFormat('id-ID');

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(
    href: NonNullable<LinkComponentBaseProps['href']>,
): string {
    return typeof href === 'string' ? href : href.url;
}

export function formatCurrency(value: number): string {
    return `Rp ${indonesiaCurrencyFormatter.format(value)}`;
}
