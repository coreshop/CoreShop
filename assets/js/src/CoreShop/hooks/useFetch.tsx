import { useState, useEffect, useCallback } from 'react';

export function useFetch<T = any>(url: string, options?: RequestInit) {
    const [data, setData] = useState<T | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    // State to trigger reload
    const [reloadFlag, setReloadFlag] = useState(0);

    const refetch = useCallback(() => {
        setReloadFlag(prev => prev + 1);
    }, []);

    useEffect(() => {
        let isCancelled = false;

        const fetchData = async () => {
            setLoading(true);
            setError(null);

            try {
                const response = await fetch(url, options);
                if (!response.ok) {
                    console.log(`HTTP error: ${response.status}`);
                }

                const json = await response.json();
                if (!isCancelled) {
                    setData(json);
                }
            } catch (err) {
                if (!isCancelled) {
                    setError((err as Error).message);
                }
            } finally {
                if (!isCancelled) {
                    setLoading(false);
                }
            }
        };

        fetchData().then(r => {});

        return () => {
            isCancelled = true;
        };
    }, [url, options, reloadFlag]);

    return { data, loading, error, refetch };
}
