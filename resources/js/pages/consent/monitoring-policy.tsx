import { Form, Head, Link } from '@inertiajs/react';
import { CheckCircle2, FileText } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import InputError from '@/components/input-error';
import { PolicyDocument } from '@/components/policy-document';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { dashboard, logout } from '@/routes';
import { accept } from '@/routes/monitoring-policy';

type Props = {
    policy: {
        id: string;
        version: number;
        title: string;
        published_at: string;
        body_html: string;
    } | null;
    mustAccept: boolean;
    acceptedAt: string | null;
};

const dateFormat = new Intl.DateTimeFormat('es-EC', {
    dateStyle: 'long',
    timeStyle: 'short',
});

export default function MonitoringPolicyPage({
    policy,
    mustAccept,
    acceptedAt,
}: Props) {
    const pendingAcceptance =
        policy !== null && mustAccept && acceptedAt === null;

    return (
        <>
            <Head title="Política de monitoreo" />

            <div className="min-h-svh bg-background px-4 py-10">
                <main className="mx-auto flex w-full max-w-3xl flex-col gap-6">
                    <div className="flex items-center gap-3">
                        <AppLogoIcon className="size-8 fill-current text-primary" />
                        <span className="font-semibold">Monitoreo</span>
                    </div>

                    {policy === null ? (
                        <Empty className="border">
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <FileText aria-hidden="true" />
                                </EmptyMedia>
                                <EmptyTitle>
                                    Todavía no hay una política publicada
                                </EmptyTitle>
                                <EmptyDescription>
                                    Cuando un Administrador la publique, la
                                    verás aquí.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl text-balance">
                                    {policy.title}
                                </CardTitle>
                                <CardDescription>
                                    Versión {policy.version} · publicada el{' '}
                                    {dateFormat.format(
                                        new Date(policy.published_at),
                                    )}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {pendingAcceptance && (
                                    <p className="mb-6 rounded-md bg-muted px-4 py-3 text-sm">
                                        Antes de usar el panel, lee la política
                                        de monitoreo de tu equipo y acéptala.
                                        Explica qué se registra, qué no y cuáles
                                        son tus derechos.
                                    </p>
                                )}
                                <PolicyDocument html={policy.body_html} />
                            </CardContent>

                            {pendingAcceptance && (
                                <CardFooter className="border-t pt-6">
                                    <Form
                                        {...accept.form()}
                                        transform={(data) => ({
                                            ...data,
                                            monitoring_policy_id: policy.id,
                                        })}
                                        className="flex w-full flex-col gap-4"
                                    >
                                        {({ processing, errors }) => (
                                            <>
                                                <div className="flex items-start gap-3">
                                                    <Checkbox
                                                        id="has-read"
                                                        name="accept"
                                                        value="1"
                                                    />
                                                    <Label
                                                        htmlFor="has-read"
                                                        className="leading-snug"
                                                    >
                                                        Leí la política de
                                                        monitoreo (versión{' '}
                                                        {policy.version}) y la
                                                        acepto.
                                                    </Label>
                                                </div>
                                                <InputError
                                                    message={
                                                        errors.accept ??
                                                        errors.monitoring_policy_id
                                                    }
                                                />
                                                <div className="flex flex-wrap items-center gap-3">
                                                    <Button
                                                        type="submit"
                                                        disabled={processing}
                                                    >
                                                        {processing && (
                                                            <Spinner />
                                                        )}
                                                        Aceptar y continuar
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={logout()}
                                                            as="button"
                                                        >
                                                            Cerrar sesión
                                                        </Link>
                                                    </Button>
                                                </div>
                                            </>
                                        )}
                                    </Form>
                                </CardFooter>
                            )}
                        </Card>
                    )}

                    {policy !== null && acceptedAt !== null && (
                        <Alert>
                            <CheckCircle2
                                aria-hidden="true"
                                className="text-success-fg"
                            />
                            <AlertTitle>
                                Aceptaste esta versión el{' '}
                                {dateFormat.format(new Date(acceptedAt))}
                            </AlertTitle>
                            <AlertDescription>
                                <Link
                                    href={dashboard()}
                                    className="underline underline-offset-4"
                                >
                                    Volver al panel
                                </Link>
                            </AlertDescription>
                        </Alert>
                    )}
                </main>
            </div>
        </>
    );
}
