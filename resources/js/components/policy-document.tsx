import { cn } from '@/lib/utils';

/**
 * Renders the Política de monitoreo. `html` comes from the server, where the
 * Administrador's Markdown is converted with raw HTML stripped and unsafe
 * links removed (MonitoringPolicy::bodyHtml), so it is safe to inject.
 */
export function PolicyDocument({
    html,
    className,
}: {
    html: string;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'space-y-4 text-sm leading-relaxed text-pretty break-words text-foreground/90',
                '[&_h1]:text-xl [&_h1]:font-semibold [&_h1]:text-foreground',
                '[&_h2]:pt-2 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:text-foreground',
                '[&_h3]:font-semibold [&_h3]:text-foreground',
                '[&_ol]:list-decimal [&_ol]:space-y-1 [&_ol]:pl-5 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-5',
                '[&_a]:text-primary [&_a]:underline [&_a]:underline-offset-4 [&_strong]:font-semibold [&_strong]:text-foreground',
                className,
            )}
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
