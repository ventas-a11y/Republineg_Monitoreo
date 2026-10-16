import type { SVGAttributes } from 'react';

/**
 * Monitoreo mark: a screen with an activity pulse. Uses currentColor, so it
 * follows `fill-current` / `text-*` like the starter-kit logo it replaces.
 */
export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                fillRule="evenodd"
                clipRule="evenodd"
                d="M6 5h28a4 4 0 0 1 4 4v18a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V9a4 4 0 0 1 4-4Zm0 4v18h28V9H6Z"
            />
            <rect x="13" y="33" width="14" height="3" rx="1.5" />
            <path
                d="M8.5 19h5.5l3-5.5 4.5 10 3-4.5h7"
                fill="none"
                stroke="currentColor"
                strokeWidth="2.5"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}
