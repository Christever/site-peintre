export const url = (path = "") => {
    const cleanPath = path.replace(/^\/+/, "");

    return `${import.meta.env.BASE_URL}/${cleanPath}`;
};