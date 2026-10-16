import { router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

/** How long before expiry the warning appears. */
const WARNING_MS = 2 * 60 * 1000;

/**
 * Warns before the session ends: after `idleMinutes` without requests, or at
 * `absoluteExpiresAt` regardless of activity (docs/SEGURIDAD.md, Q51). Every
 * Inertia visit is activity, so the idle deadline restarts on each page.
 */
export function SessionTimeoutWarning() {
    const page = usePage();
    const sessionTimeout = page.props.sessionTimeout;
    const [deadline, setDeadline] = useState<number | null>(null);
    const [secondsLeft, setSecondsLeft] = useState<number | null>(null);
    const [dismissedDeadline, setDismissedDeadline] = useState<number | null>(
        null,
    );

    useEffect(() => {
        if (sessionTimeout === null) {
            setDeadline(null);

            return;
        }

        setDeadline(
            Math.min(
                Date.now() + sessionTimeout.idleMinutes * 60 * 1000,
                sessionTimeout.absoluteExpiresAt * 1000,
            ),
        );
    }, [page, sessionTimeout]);

    useEffect(() => {
        if (deadline === null) {
            setSecondsLeft(null);

            return;
        }

        const tick = () => {
            const remaining = deadline - Date.now();

            if (remaining <= 0) {
                // The server session is gone: reloading lands on the login page.
                router.reload();

                return;
            }

            setSecondsLeft(
                remaining <= WARNING_MS ? Math.ceil(remaining / 1000) : null,
            );
        };

        tick();
        const interval = window.setInterval(tick, 1000);

        return () => window.clearInterval(interval);
    }, [deadline]);

    if (
        sessionTimeout === null ||
        secondsLeft === null ||
        dismissedDeadline === deadline
    ) {
        return null;
    }

    const isAbsolute =
        deadline !== null &&
        deadline >= sessionTimeout.absoluteExpiresAt * 1000;
    const minutes = Math.floor(secondsLeft / 60);
    const seconds = String(secondsLeft % 60).padStart(2, '0');

    return (
        <Dialog
            open
            onOpenChange={(open) => {
                if (!open) {
                    setDismissedDeadline(deadline);
                }
            }}
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Tu sesión está por vencer</DialogTitle>
                    <DialogDescription>
                        {isAbsolute
                            ? 'Llegaste al máximo de 8 horas de sesión. Guarda lo que estés haciendo; tendrás que iniciar sesión otra vez.'
                            : 'Por seguridad, la sesión se cierra tras 30 minutos sin actividad.'}
                    </DialogDescription>
                </DialogHeader>
                <p className="text-center text-3xl font-semibold tabular-nums">
                    {minutes}:{seconds}
                </p>
                {!isAbsolute && (
                    <DialogFooter>
                        <Button onClick={() => router.reload()}>
                            Seguir conectado
                        </Button>
                    </DialogFooter>
                )}
            </DialogContent>
        </Dialog>
    );
}
