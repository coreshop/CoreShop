import {message} from "antd";

export function useCountryActions() {
    const createCountry = async (name: string, url: string) => {
        const payload = { name };

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                message.error('Error creating country');
                return false;
            }

            message.success('Country created successfully');
            return true;
        } catch (error) {
            message.error('Failed to create country');
            return false;
        }
    };

    const updateCountry = async (values: any, id: number, url: string) => {
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
                message.error('Error updating country');
                return false;
            }

            message.success('Country updated successfully');
            return true;
        } catch (error) {
            message.error('Failed to update country');
            return false;
        }
    };

    const deleteCountry = async (id: number, url: string) => {
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
                message.error('Error deleting country');
                return false;
            }

            message.success('Country deleted successfully');
            return true;
        } catch (error) {
            message.error('Failed to delete country');
            return false;
        }
    };

    return {
        createCountry,
        updateCountry,
        deleteCountry
    };
}