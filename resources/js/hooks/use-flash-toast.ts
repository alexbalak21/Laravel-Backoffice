import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

type FlashMessages = {
    success?: string;
    error?: string;
    info?: string;
    warning?: string;
};

export default function useFlashToast() {
    const { flash } = usePage().props as { flash?: FlashMessages };

    useEffect(() => {
        if (!flash) return;

        const showToast = (
            type: keyof FlashMessages,
            fn: (msg: string) => void,
        ) => {
            const message = flash[type];
            if (message) {
                fn(message);
                router.visit(window.location.pathname, {
                    only: [],
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        window.history.replaceState(
                            {},
                            document.title,
                            window.location.pathname,
                        );
                    },
                });
            }
        };

        showToast('success', toast.success);
        showToast('error', toast.error);
        showToast('info', toast.info);
        showToast('warning', toast.warning);
    }, [flash]);
}
