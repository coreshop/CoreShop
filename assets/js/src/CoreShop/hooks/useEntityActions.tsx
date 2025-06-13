import { useFormModal } from '@pimcore/studio-ui-bundle/components';

type EntityWithId = {
    id: number;
};

type UseEntityActionsOptions<T extends EntityWithId> = {
    createEndpoint: string;
    deleteEndpoint: string;
    createFn: (value: string, endpoint: string) => Promise<unknown>;
    deleteFn: (id: number, endpoint: string) => Promise<unknown>;
    refetch: () => void;
    getSelected: () => T | null;
    clearSelected: () => void;
};

export const useEntityActions = <T extends EntityWithId>({
     createEndpoint,
     deleteEndpoint,
     createFn,
     deleteFn,
     refetch,
     getSelected,
     clearSelected,
 }: UseEntityActionsOptions<T>) => {
    const { input } = useFormModal();

    const handleCreate = async (value: string) => {
        await createFn(value, createEndpoint);
        refetch();
    };

    const handleDelete = async (id: number) => {
        await deleteFn(id, deleteEndpoint);
        if (getSelected()?.id === id) {
            clearSelected();
        }
        refetch();
    };

    const openCreateModal = () => {
        input({
            title: 'New Entry',
            label: 'Name',
            rule: {
                required: true,
                message: 'Please enter a name',
            },
            okText: 'Create',
            onOk: handleCreate,
        });
    };

    return {
        handleCreate,
        handleDelete,
        openCreateModal,
    };
};
