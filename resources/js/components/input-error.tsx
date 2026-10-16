import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

/**
 * Inline field error. `role="alert"` makes screen readers announce it when
 * it appears after submitting.
 */
export default function InputError({
    message,
    className = '',
    ...props
}: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <p
            role="alert"
            {...props}
            className={cn('text-sm text-destructive-foreground', className)}
        >
            {message}
        </p>
    ) : null;
}
