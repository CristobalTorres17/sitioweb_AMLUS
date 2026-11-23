import { useEffect, useRef, useState } from 'react';
import axios from 'axios';

interface AutoSaveState {
    isSaving: boolean;
    lastSaved: Date | null;
    error: string | null;
}

interface UseAutoSaveProps {
    articleId?: number;
    enabled: boolean;
    intervalMs?: number;
    onSuccess?: (response: any) => void;
    onError?: (error: any) => void;
}

/**
 * Hook para auto-guardar borradores cada N segundos
 * Llama a POST /articles/draft (nuevo) o POST /articles/{id}/draft (existente)
 * @param articleId - ID del artículo a guardar (undefined para crear nuevo)
 * @param enabled - Si está habilitado el autosave
 * @param intervalMs - Intervalo en milisegundos (default 30000 = 30 segundos)
 * @param onSuccess - Callback cuando se guarda exitosamente
 * @param onError - Callback cuando hay error
 */
export function useAutoSave({
    articleId,
    enabled = false,
    intervalMs = 30000,
    onSuccess,
    onError,
}: UseAutoSaveProps) {
    const [state, setState] = useState<AutoSaveState>({
        isSaving: false,
        lastSaved: null,
        error: null,
    });

    const timerRef = useRef<NodeJS.Timeout | null>(null);
    const dataRef = useRef<any>(null);

    // Actualizar los datos a guardar (llamado desde el componente)
    const setDataToSave = (data: any) => {
        dataRef.current = data;
    };

    // Ejecutar el autosave
    const performAutoSave = async () => {
        if (!enabled || !dataRef.current) {
            return;
        }

        setState(prev => ({ ...prev, isSaving: true, error: null }));

        try {
            const url = articleId ? `/articles/${articleId}/draft` : '/articles/draft';

            const response = await axios.post(url, dataRef.current);

            setState(prev => ({
                ...prev,
                isSaving: false,
                lastSaved: new Date(),
                error: null,
            }));

            onSuccess?.(response.data);
        } catch (error: any) {
            const errorMessage = error.response?.data?.message || 'Error saving draft';
            setState(prev => ({
                ...prev,
                isSaving: false,
                error: errorMessage,
            }));

            onError?.(error);
        }
    };

    // Setup timer
    useEffect(() => {
        if (!enabled) {
            if (timerRef.current) {
                clearInterval(timerRef.current);
                timerRef.current = null;
            }
            return;
        }

        // Realizar autosave periódicamente
        timerRef.current = setInterval(() => {
            performAutoSave();
        }, intervalMs);

        // Cleanup
        return () => {
            if (timerRef.current) {
                clearInterval(timerRef.current);
            }
        };
    }, [enabled, intervalMs]);

    // Guardar inmediatamente en el componentWillUnmount
    useEffect(() => {
        return () => {
            // Cuando el componente se desmonta, guardar si hay datos pendientes
            // (opcional: comentar si no quieres guardar al salir)
            // performAutoSave();
        };
    }, []);

    return {
        isSaving: state.isSaving,
        lastSaved: state.lastSaved,
        error: state.error,
        setDataToSave,
        performAutoSave, // Permite guardar manualmente si es necesario
    };
}
