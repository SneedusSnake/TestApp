export async function fetchClients(filters) {
    let url = 'http://localhost/api/clients'
    if (filters) {
        if (filters.ids) {
            url += '?' + new URLSearchParams(filters.ids.map(id => ['ids[]', id]))
        } else {
            url += '?' + new URLSearchParams(filters)
        }
   }

    const response = await fetch(url)

    return await response.json()
}

export async function createClient(client) {
    const response = await fetch('http://localhost/api/clients', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'accept': 'application/json'
        },
        body: JSON.stringify(client)
    })

    return await response.json()
}
