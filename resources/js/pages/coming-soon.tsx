import { Head } from '@inertiajs/react';
import { Hammer } from 'lucide-react';
import Heading from '@/components/heading';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';

type Props = {
    title: string;
    path: string;
    description: string;
    ticket: string;
};

/**
 * Empty state for a panel screen whose feature has not landed yet.
 */
export default function ComingSoon({ title, description, ticket }: Props) {
    return (
        <>
            <Head title={title} />

            <div className="space-y-6 p-4">
                <Heading title={title} description={description} />

                <Empty className="border">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <Hammer aria-hidden="true" />
                        </EmptyMedia>
                        <EmptyTitle>
                            Esta pantalla está en construcción
                        </EmptyTitle>
                        <EmptyDescription>
                            Llega con el {ticket} del roadmap. Mientras tanto no
                            muestra datos.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>
            </div>
        </>
    );
}

ComingSoon.layout = (props: Props) => ({
    breadcrumbs: [{ title: props.title, href: props.path }],
});
