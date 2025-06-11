import {message} from "antd";

export function useStateActions() {
    const createState = async (name: string, url: string) => {
        const payload = { name };

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                message.error('Error creating State');
                return false;
            }

            message.success('State created successfully');
            return true;
        } catch (error) {
            message.error('Failed to state country');
            return false;
        }
    };

    const updateState = async (values: any, id: number, url: string) => {
        const payload = {
            ...values,
            id: id,
        };

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                message.error('Error updating state');
                return false;
            }

            message.success('State updated successfully');
            return true;
        } catch (error) {
            message.error('Failed to update state');
            return false;
        }
    };

    const deleteState = async (id: number, url: string) => {
        const payload = {
            id: id,
        };

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                message.error('Error deleting state');
                return false;
            }

            message.success('Country state successfully');
            return true;
        } catch (error) {
            message.error('Failed to delete country');
            return false;
        }
    };

    return {
        createState,
        updateState,
        deleteState
    };
}