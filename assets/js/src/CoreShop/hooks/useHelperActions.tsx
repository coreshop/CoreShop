import { message } from "antd";

type HttpMethod = 'POST' | 'DELETE';

async function performRequest(
    url: string,
    payload: object,
    method: HttpMethod,
    successMsg: string,
    errorMsg: string
): Promise<boolean> {
    try {
        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            message.error(errorMsg);
            return false;
        }

        message.success(successMsg);
        return true;
    } catch (error) {
        message.error(errorMsg);
        return false;
    }
}

export function useHelperActions(entityName: string) {
    const create = async (name: string, url: string) => {
        return performRequest(
            url,
            { name },
            'POST',
            `${entityName} created successfully`,
            `Error creating ${entityName}`
        );
    };

    const update = async (values: any, id: number, url: string) => {
        return performRequest(
            url,
            { ...values, id },
            'POST',
            `${entityName} updated successfully`,
            `Error updating ${entityName}`
        );
    };

    const remove = async (id: number, url: string) => {
        return performRequest(
            url,
            { id },
            'DELETE',
            `${entityName} deleted successfully`,
            `Error deleting ${entityName}`
        );
    };

    return { create, update, remove };
}
