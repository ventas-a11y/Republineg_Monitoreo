import type { Auth, SessionTimeout } from '@/types/auth';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sessionTimeout: SessionTimeout | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
