export async function fetchClients(ids = []) {
    let url = 'http://localhost/api/clients'
    if (ids) {
        url += '?' + new URLSearchParams(ids.map(id => ['ids[]', id]))
    }
    const response = await fetch(url)
    const json = await response.json()

    return json.data
}
