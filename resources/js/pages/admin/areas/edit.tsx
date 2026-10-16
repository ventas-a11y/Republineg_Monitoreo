import { Form, Head } from '@inertiajs/react';
import AreaController from '@/actions/App/Http/Controllers/Admin/AreaController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/admin/areas';

type Props = {
    area: { id: string; name: string };
};

export default function EditArea({ area }: Props) {
    return (
        <>
            <Head title={`Renombrar ${area.name}`} />

            <div className="p-4">
                <Heading title="Renombrar Área" />

                <Form
                    {...AreaController.update.form(area.id)}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nombre</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    defaultValue={area.name}
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <Button type="submit" disabled={processing}>
                                Guardar
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EditArea.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Áreas', href: index() },
        { title: props.area.name, href: edit(props.area.id) },
    ],
});
