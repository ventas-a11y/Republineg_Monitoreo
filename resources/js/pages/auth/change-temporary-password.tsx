import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/temporary-password';

type Props = {
    passwordRules: string;
};

export default function ChangeTemporaryPassword({ passwordRules }: Props) {
    return (
        <>
            <Head title="Cambia tu contraseña" />

            <Form
                {...update.form()}
                resetOnSuccess={['password', 'password_confirmation']}
            >
                {({ processing, errors }) => (
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="password">Nueva contraseña</Label>
                            <PasswordInput
                                id="password"
                                name="password"
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                autoFocus
                                passwordrules={passwordRules}
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">
                                Confirma la contraseña
                            </Label>
                            <PasswordInput
                                id="password_confirmation"
                                name="password_confirmation"
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                passwordrules={passwordRules}
                            />
                            <InputError
                                message={errors.password_confirmation}
                            />
                        </div>

                        <Button
                            type="submit"
                            className="mt-4 w-full"
                            disabled={processing}
                            data-test="change-temporary-password-button"
                        >
                            {processing && <Spinner />}
                            Guardar contraseña
                        </Button>
                    </div>
                )}
            </Form>
        </>
    );
}

ChangeTemporaryPassword.layout = {
    title: 'Cambia tu contraseña temporal',
    description:
        'Elige una contraseña propia de al menos 8 caracteres. Después configurarás tu segundo factor.',
};
