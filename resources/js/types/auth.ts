export type Role = 'administrador' | 'revisor' | 'empleado';

export type User = {
    id: string;
    name: string;
    email: string;
    role: Role;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    can: {
        viewMonitoring: boolean;
        manageAccounts: boolean;
        manageAreas: boolean;
    };
};

export type SessionTimeout = {
    /** Minutes of inactivity before the session expires. */
    idleMinutes: number;
    /** Unix timestamp (seconds) when the login expires regardless of activity. */
    absoluteExpiresAt: number;
};

export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
