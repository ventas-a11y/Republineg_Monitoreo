import { Head, Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { dashboard, login } from '@/routes';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Monitoreo" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-background p-6 text-foreground">
                <main className="flex w-full max-w-xl flex-col items-center gap-6 text-center">
                    <h1 className="text-2xl font-semibold tracking-tight sm:text-3xl">
                        Monitoreo de equipos de Intelicorp
                    </h1>
                    <p className="text-muted-foreground">
                        Herramienta interna. El monitoreo es transparente: cada
                        persona puede ver lo que se registra de su propia
                        actividad.
                    </p>
                    {auth.user ? (
                        <Button asChild size="lg">
                            <Link href={dashboard()}>Ir al panel</Link>
                        </Button>
                    ) : (
                        <Button asChild size="lg">
                            <Link href={login()}>Iniciar sesión</Link>
                        </Button>
                    )}
                </main>
            </div>
        </>
    );
}
