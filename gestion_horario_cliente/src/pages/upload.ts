import type { APIContext } from 'astro';

export async function post({ request }: APIContext) {
    const formData = await request.formData();
    const files = formData.getAll('xml'); //  'xml' es el nombre del campo para cargar el fiechero

    const fileDetails = await Promise.all(files.map(async (file: File) => {
        return {
            webkitRelativePath: file.webkitRelativePath,
            lastModified: file.lastModified,
            name: file.name,
            size: file.size,
            type: file.type,
            buffer: Array.from(new Int8Array(await file.arrayBuffer())),
        };
    }));

    return {
        body: JSON.stringify({
            fileNames: fileDetails,
        }),
    };
}
