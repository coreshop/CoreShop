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

    return {
        createCountry,
        updateCountry,
    };
}